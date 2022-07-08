<template>
    <v-dialog
    v-model="addCategoryModal"
    persistent
    max-width="700px"
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
            color="primary"
            dark
            v-bind="attrs"
            v-on="on"
            class="mx-2 mt-3"
            >
                <v-icon>{{icons.mdiPlusCircle }}</v-icon>
                &nbsp; Add Category
            </v-btn>

        </template>
        <v-card>
            <v-card-title>
            <span class="text-h5">Add Category</span>
            </v-card-title>
            <v-card-text>
            <v-container>
                <v-row>
                    <v-col cols="6">
                        <v-select
                        v-model="selectedPlatform"
                        :items="platforms"
                        item-text="name"
                        item-value="value"
                        label="Platform"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="6">
                        <v-select
                        v-model="selectedType"
                        :items="types"
                        item-text="name"
                        item-value="value"
                        label="Category Type"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="12" v-if="selectedPlatform === 'mobile'">
                        <v-text-field
                            v-model="categoryID"
                            label="Category ID"
                            placeholder="S001"
                            required
                            outlined
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                            v-model="categoryName"
                            label="Category Name"
                            placeholder="Makanan"
                            required
                            outlined
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-container>
            </v-card-text>
            <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
                color="warning"
                x-large
                @click="addCategoryModal = false"
            >
                Close
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
                color="primary"
                x-large
                @click="addCategory"
            >
                Add Category
            </v-btn>
            <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { mdiPlusCircle  } from '@mdi/js';


export default {
    data() {
        return {
            platforms: ['desktop', 'mobile'],
            selectedPlatform: null,
            types: ['makanan', 'minuman', 'non-konsumsi'],
            selectedType: null,
            addCategoryModal: false,
            categoryName: '',
            categoryID: '',
            icons:{
                mdiPlusCircle ,
            }
        }
    },
    methods: {
        addCategory(){
            //
            if (this.selectedPlatform === 'desktop') {
                //
                const formData = new FormData();
                formData.append("category_id", this.category_id);
                formData.append("name", this.categoryName);
                formData.append("type", this.selectedType);
                axios.post('http://localhost:8099/api/add-category', formData)
                .then((response) => {
                    const formData = new FormData();
                    formData.append("category_id", response.data.id);
                    formData.append("name", this.categoryName);
                    formData.append("type", this.selectedType);
                    axios.post('add-category-desktop', formData)
                    .then((response) => {
                        Vue.$toast.open({
                            message: "category added successfuly",
                            type: 'success',
                            position: 'top',
                            duration: 2000,
                        });
                        this.refreshData();
                        this.addCategoryModal = false;
                    }).catch((error) => {
                        Vue.$toast.open({
                            message: error,
                            type: 'error',
                            position: 'top',
                            duration: 2000,
                        });
                    })
                }).catch((error) => {
                    Vue.$toast.open({
                        message: error,
                        type: 'error',
                        position: 'top',
                        duration: 2000,
                    });
                })
            } else if (this.selectedPlatform === 'mobile') {
                //
                Vue.$toast.open({
                    message: "mobile feature not ready yet",
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });
            }

        },
        refreshData(){
            this.$emit('getData');
        }
    },
}
</script>