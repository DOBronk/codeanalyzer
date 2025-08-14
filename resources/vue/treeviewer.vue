<template>
  <div class="card max-w-7xl">
    <form :action="route" method="post">
      <div class="flex items-center justify-center">
        <div class="flex flex-col gap-2 w-6/20">
          <label for="repo">Eigenaar</label>
          <InputText v-model="owner" :value="owner" name="owner" :invalid="!validOwner" class="w-9/10" />
        </div>
        <div class="flex flex-col gap-2 w-6/20">
          <label for="repo">Repository</label>
          <Select v-model="selectedRepository" @change="changeBranch" :options="repositories" :placeholder="repository"
            class="w-9/10" :disabled="!validOwner" />
          <input type="hidden" name="repository" :value="selectedRepository" />
        </div>
        <div class="flex flex-col gap-2 w-5/20">
          <label for="branch">Branch</label>
          <Select v-model="selectedBranch" @change="changeTree" :options="cbranch" :placeholder="branch" class="w-9/10"
            :disabled="!validOwner" />
          <input type="hidden" name="branch" :value="selectedBranch" />
        </div>
        <div class="flex flex-col right-0 w-3/20">
          <Button class="bg-blue-600 text-white mt-7 py-2 rounded-sm hover:bg-blue-700 transition" severity="info"
            :disabled="!canSubmit" type="submit">
            Verzenden
          </Button>
        </div>
      </div>
      <div class="w-full mt-5 mb-5">
        <Message v-if="!validOwner" severity="error">
          Geen geldige repository eigenaar ingevuld
        </Message>
        <Message v-else-if="nodes && !canSubmit" severity="info">
          Selecteer eerst bestanden om verder te gaan
        </Message>
        <Message v-else-if="canSubmit" severity="success">
          Bestanden geselecteerd: {{ countFiles }}
        </Message>

      </div>
      <input type="hidden" name="_token" :value="csrf" />
      <input type="hidden" name="owner" :value="owner" />
      <div v-if="nodes == null" class="flex items-center mt-30">
        <ProgressSpinner />
      </div>

      <TreeTable v-model:selectionKeys="selectedKey" :value="nodes" v-if="nodes" selectionMode="checkbox"
        tableStyle="min-width: 50rem" name="test">
        <template #header>
          <div class="text-xl font-bold">Github tree bestanden {{ selectedRepository }} {{ selectedBranch }} </div>
        </template>
        <Column field="name" header="Naam" expander style="width: 34%"></Column>
        <Column field="size" header="Grootte" style="width: 33%"></Column>
        <Column field="type" header="Type" style="width: 33%" sortable></Column>
      </TreeTable>

      <template v-for="(x, index) in selectedKey">
        <template v-if="!index.includes(':folder:')">
          <input type="hidden" :name="'selections[' + curIndex.toString() + '][path]'" :value="index.split(':')[0]" />
          <input type="hidden" :name="'selections[' + (curIndex++).toString() + '][sha]'"
            :value='index.split(":")[1]' />
        </template>
      </template>
    </form>
  </div>
</template>

<script setup>
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Message from "primevue/message";
import ProgressSpinner from "primevue/progressspinner";
import TreeTable from "primevue/treetable";
import Column from "primevue/column";
import _debounce from "lodash/debounce";
import { ref, watch, computed } from "vue";

const props = defineProps(["csrf", "owner", "route"]);

const nodes = ref(null),
  branches = ref([null]),
  selectedBranch = ref(),
  selectedRepository = ref(),
  selectedKey = ref(),
  owner = ref(props.owner),
  repositories = ref(),
  cbranch = ref();

let mainBranches = [];
let trees = {};
let curIndex = 0;

const validOwner = computed(() => repositories.value == null ? false : true),
  canSubmit = computed(() => countFiles.value > 0),
  selectedIndex = computed(() => repositories.value.length > 0 ? repositories.value.findIndex((repo) => repo == selectedRepository.value) : 0),
  countFiles = computed(() => {
    return selectedKey.value ? Object.keys(selectedKey.value).filter((val) => !val.includes('folder')).length : 0;
  });

// Reset de index van geselecteerde bestanden zodra er een wijziging
// optreed. Zo begint de v-for van de hidden input field weer correct.
watch(selectedKey, (newVal) => {
  curIndex = 0;
});

watch(owner, (newVal) => {
  updateModel(newVal);
});

watch(nodes, (newVal) => {
  if (newVal == null) {
    selectedKey.value = null;
  }
});

const updateModel = _debounce((newVal) => {
  getRepositories(newVal);
}, 500);

function changeBranch() {
  nodes.value = null;
  let index = selectedIndex.value;

  if (branches.value[index] == null) {
    axios
      .post("/getbranches", {
        owner: owner.value,
        repository: repositories.value[index],
      })
      .then((response) => {
        branches.value[index] = response.data;
        cbranch.value = branches.value[index];
        selectedBranch.value = mainBranches.value[index];
        changeTree();
      }).catch((error) => handleError(error));
  } else {
    cbranch.value = branches.value[index]
    selectedBranch.value = mainBranches.value[index];
    changeTree();
  }
}

function getRepositories(ownerRepo) {
  nodes.value = null;
  repositories.value = null;

  axios.post("/getrepositories", { owner: ownerRepo }).then(function (response) {
    repositories.value = response.data[0];
    mainBranches.value = response.data[1];

    selectedRepository.value = repositories.value[0];
    changeBranch();
  }).catch((error) => handleError(error))
}

function changeTree() {
  if ((nodes.value = getIfExists(owner.value, selectedRepository.value, selectedBranch.value, true)) == null) {
    axios
      .post("/gettree", {
        owner: owner.value,
        branch: selectedBranch.value,
        repository: selectedRepository.value,
      })
      .then(function (response) {
        nodes.value = response.data;
        saveTree(owner.value, selectedRepository.value, selectedBranch.value);
      }).catch((error) => handleError(error));
  }
}

function getIfExists(own, rep, bra) {
  if (own in trees && rep in trees[own]) {
    if (bra in trees[own][rep]) {
      return trees[own][rep][bra];
    }
  }
  return null;
}

function saveTree(own, rep, bra) {
  if (own in trees) {
    if (rep in trees[own]) {
      trees[own][rep][bra] = nodes.value;
    } else {
      trees[own][rep] = { [bra]: nodes.value };
    }
  } else {
    trees[own] = { [rep]: { [bra]: nodes.value } };
  }
}

function handleError(error) {
  if (error.response.status == 401) {
    location.replace(location.protocol + "//" + location.hostname + "/login");
  }
}
</script>
