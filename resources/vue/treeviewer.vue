<template>
  <div class="card max-w-7xl">
    <form :action="route" method="post">
      <div class="flex items-center justify-center">
        <div class="flex flex-col gap-2 w-6/20">
          <label for="repo">Eigenaar</label>
          <InputText v-model="owner" :value="owner" name="owner" :invalid="!validOwner" :disabled="ownerLocked"
            class="w-9/10" />
        </div>
        <div class="flex flex-col gap-2 w-6/20">
          <label for="repo">Repository</label>
          <Select v-model="selectedRepository" @change="branchChange" :options="repositories" :placeholder="repository"
            filter optionLabel="name" optionValue="name" class="w-9/10" :disabled="!validOwner" />
          <input type="hidden" name="repository" :value="selectedRepository" />
        </div>
        <div class="flex flex-col gap-2 w-5/20">
          <label for="branch">Branch</label>
          <Select v-model="selectedBranch" @change="changeTree" :options="branches" :placeholder="branch"
            optionLabel="name" optionValue="name" class="w-9/10" :disabled="!validOwner" />
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
          <input type="hidden" :name="nameMe(false, 'path')" :value="splitMe(index, 0)" />
          <input type="hidden" :name="nameMe(true, 'sha')" :value="splitMe(index, 1)" />
        </template>
      </template>
    </form>
  </div>
</template>

<script setup>
import Button from "primevue/button";
import Select from "primevue/select";
import Message from "primevue/message";
import ProgressSpinner from "primevue/progressspinner";
import _debounce from "lodash/debounce";
import { ref, watch, computed } from "vue";

const props = defineProps(["csrf", "owner", "route"]);

const nodes = ref(null),
  branches = ref(),
  selectedBranch = ref(),
  selectedRepository = ref(),
  selectedKey = ref(),
  mainBranch = ref(),
  owner = ref(props.owner),
  repositories = defineModel(),
  ownerLocked = props.owner ? true : false;

const validOwner = computed(() => {
  if (ownerLocked) {
    return true;
  }
  return repositories?.value == null ? false : true;
}),
  canSubmit = computed(() => countFiles.value > 0),
  countFiles = computed(() => {
    return selectedKey.value ? Object.keys(selectedKey.value).filter((val) => !val.includes('folder')).length : 0;
  });

let trees = {}; // [props.repository]: { [props.branch]: props.nodes }
let curIndex = 0;

if (ownerLocked) {
  getRepositories(owner.value);
} else {
  watch(owner, (newVal) => {
    updateModel(newVal);
  });
}

watch(nodes, (newVal) => {
  if (newVal == null) {
    selectedKey.value = null;
  }
});

watch(selectedKey, (newVal) => {
  curIndex = 0;
});

const updateModel = _debounce((newVal) => {
  getRepositories(newVal);
}, 500);

function splitMe(value, index) {
  return value.split(":")[index];
}

function nameMe(last, name) {
  let result = "selections[" + curIndex.toString() + "][" + name + "]";
  if (last) {
    curIndex++;
  }
  return result;
}

function handleError(error) {
  if (error.response.status == 401) {
    location.replace(location.protocol + "//" + location.hostname + "/login");
  }
}

function branchChange() {
  nodes.value = null;
  axios
    .post("http://localhost/getbranches", {
      owner: owner.value,
      repository: selectedRepository.value,
    })
    .then((response) => {
      var een = Object.values(response.data)[0];
      branches.value = response.data;
      selectedBranch.value = Object.values(een)[0];
      getDefault(owner.value, selectedRepository.value);
    }).catch((error) => handleError(error));
}

function getRepositories(ownerRepo) {
  nodes.value = null;
  repositories.value = null;

  axios.post("/getrepositories", { owner: ownerRepo }).then(function (response) {
    repositories.value = response.data;
    var een = Object.values(response.data)[0];
    selectedRepository.value = Object.values(een)[0];
    branchChange();
  }).catch((error) => handleError(error))
}

function getDefault(own, repo) {
  //own ??= owner.value;
  //repo ??= selectedRepository.value;
  axios.post("/getdefault", { owner: own, repository: repo }).then(function (response) {
    mainBranch.value = response.data;
    selectedBranch.value = response.data;
    changeTree();
  });
}

function changeTree() {
  if (selectedRepository.value in trees && selectedBranch.value in trees[selectedRepository.value]) {
    nodes.value = trees[selectedRepository.value][selectedBranch.value];
  } else {
    nodes.value = null;
    changeRef();
  }
}

function saveTree(rep, bra) {
  if (rep in trees) {
    trees[rep][bra] = nodes.value;
  } else {
    trees[rep] = { [bra]: nodes.value };
  }
}

function changeRef() {
  axios
    .post("http://localhost/gettree", {
      owner: owner.value,
      branch: selectedBranch.value,
      repository: selectedRepository.value,
    })
    .then(function (response) {
      nodes.value = response.data;
      saveTree(selectedRepository.value, selectedBranch.value);
    }).catch((error) => handleError(error));
}

</script>
