<template>
    <div class="card max-w-7xl">
        <Message v-if="error" severity="error" class="mb-5">
            {{ error[0] }}
        </Message>
        <form :action="route" method="post">
            <Panel header="Gegevens">
                <div class="flex items-center justify-center">
                    <div class="flex flex-col gap-2 w-6/20">
                        <label for="owner">Eigenaar</label>
                        <InputText v-model="owner" name="owner" :invalid="!validOwner" class="w-9/10" />
                    </div>

                    <div class="flex flex-col gap-2 w-6/20">
                        <label for="repo">Repository</label>
                        <Select v-model="selectedRepository" @change="changeBranch" :options="repositories"
                            :placeholder="repository" class="w-9/10" :disabled="!validOwner" />
                        <input type="hidden" name="repository" :value="selectedRepository" />
                    </div>

                    <div class="flex flex-col gap-2 w-5/20">
                        <label for="branch">Branch</label>
                        <Select v-model="selectedBranch" @change="changeTree" :options="branchOptions"
                            :placeholder="branch" class="w-9/10" :disabled="!validOwner" />
                        <input type="hidden" name="branch" :value="selectedBranch" />
                    </div>

                    <div class="flex flex-col right-0 w-3/20">
                        <Button class="bg-blue-600 text-white mt-7 py-2 rounded-sm hover:bg-blue-700 transition"
                            severity="info" :disabled="noFolders.length === 0" type="submit">
                            Verzenden
                        </Button>
                    </div>
                </div>

                <div class="w-full mt-5">
                    <Message v-if="!validOwner" severity="error">
                        Geen geldige repository eigenaar ingevuld
                    </Message>
                    <Message v-else-if="nodes && noFolders.length === 0" severity="info">
                        Selecteer eerst bestanden om verder te gaan
                    </Message>
                    <Message v-else-if="noFolders.length > 0" severity="success">
                        Bestanden geselecteerd: {{ noFolders.length }}
                    </Message>
                </div>
            </Panel>

            <input type="hidden" name="_token" :value="csrf" />

            <Panel header="Bestanden" class="mt-5">
                <div v-if="nodes == null" class="flex items-center mt-5">
                    <ProgressSpinner />
                </div>

                <TreeTable v-if="nodes" v-model:selectionKeys="selectedKey" :value="nodes" selectionMode="checkbox"
                    tableStyle="min-width: 50rem">
                    <Column field="name" header="Naam" expander style="width: 34%" sortable />
                    <Column field="size" header="Grootte" style="width: 33%" sortable />
                    <Column field="type" header="Type" style="width: 33%" sortable />

                    <template #footer>
                        <div class="flex flex-col right-0 w-3/20">
                            <Button class="bg-blue-600 text-white mt-7 py-2 rounded-sm hover:bg-blue-700 transition"
                                severity="info" :disabled="noFolders.length === 0" type="submit">
                                Verzenden
                            </Button>
                        </div>
                    </template>
                </TreeTable>
            </Panel>

            <template v-for="(val, k) in noFolders" :key="k">
                <input type="hidden" :name="`selections[${k}][path]`" :value="(val.split(':'))[0]" />
                <input type="hidden" :name="`selections[${k}][sha]`" :value="(val.split(':'))[1]" />
            </template>
        </form>
    </div>
</template>

<script setup>
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Message from "primevue/message";
import Panel from "primevue/panel";
import ProgressSpinner from "primevue/progressspinner";
import TreeTable from "primevue/treetable";
import Column from "primevue/column";
import _debounce from "lodash/debounce";
import { ref, watch, computed } from "vue";

const props = defineProps(["csrf", "owner", "route", "error"]);

const nodes = ref(null);
const branches = ref([null]);
const owner = ref(props.owner);
const repositories = ref();
const branchOptions = ref();
const selectedBranch = ref();
const selectedRepository = ref();
const selectedKey = ref();
const defaultBranches = ref([]);

const validOwner = computed(() => Array.isArray(repositories.value) && repositories.value.length > 0);
const selectedIndex = computed(() =>
    repositories.value?.length > 0
        ? repositories.value.findIndex((repo) => repo === selectedRepository.value)
        : 0
);
const noFolders = computed(() =>
    selectedKey.value
        ? Object.keys(selectedKey.value).filter((key) => !key.includes(":folder:"))
        : []
);

let trees = {};

watch(owner, _debounce((newVal) => {
    getRepositories(newVal);
}, 500));

watch(nodes, (newVal) => {
    if (newVal == null) {
        selectedKey.value = null;
    }
});

function changeBranch() {
    const index = selectedIndex.value;

    if (!branches.value[index]) {
        axios
            .get("/getbranches/" + owner.value + "/" + repositories.value[index])
            .then((response) => {
                branches.value[index] = response.data;
                branchOptions.value = branches.value[index];
                selectedBranch.value = defaultBranches.value[index];
                changeTree();
            })
            .catch(handleError);
    } else {
        branchOptions.value = branches.value[index];
        selectedBranch.value = defaultBranches.value[index];
        changeTree();
    }
}

function getRepositories(ownerRepo) {
    nodes.value = null;
    repositories.value = null;
    branches.value = [];

    axios
        .post("/getrepositories", { owner: ownerRepo })
        .then((response) => {
            repositories.value = response.data[0];
            defaultBranches.value = response.data[1];
            selectedRepository.value = repositories.value[0];
            changeBranch();
        })
        .catch(handleError);
}

function changeTree() {
    nodes.value = null;

    const cachedTree = getIfExists(owner.value, selectedRepository.value, selectedBranch.value);
    if (cachedTree) {
        nodes.value = cachedTree;
        return;
    }

    axios
        .post("/gettree", {
            owner: owner.value,
            branch: selectedBranch.value,
            repository: selectedRepository.value,
        })
        .then((response) => {
            nodes.value = response.data;
            saveTree(owner.value, selectedRepository.value, selectedBranch.value);
        })
        .catch(handleError);
}

function getIfExists(own, rep, bra) {
    selectedKey.value = null;
    return trees?.[own]?.[rep]?.[bra] ?? null;
}

function saveTree(own, rep, bra) {
    trees[own] ??= {};
    trees[own][rep] ??= {};
    trees[own][rep][bra] = nodes.value;
}

function handleError(error) {
    if (error.response?.status === 401) {
        location.replace(`${location.protocol}//${location.hostname}/login`);
    }
}
</script>