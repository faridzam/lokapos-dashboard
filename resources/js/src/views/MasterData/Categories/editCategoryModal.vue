<template>
    <v-dialog
    v-model="editCategoryModal"
    persistent
    max-width="700px"
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
            color="primary"
            fab
            small
            v-bind="attrs"
            v-on="on"
            >
                <v-icon>{{icons.mdiCogOutline}}</v-icon>
            </v-btn>

        </template>
        <v-card>
            <v-card-title>
            <span class="text-h5">{{name}}</span>
            </v-card-title>
            <v-card-text>
            <v-container>
                <v-row>
                    <v-col cols="6">
                        <v-select
                        :value="platform"
                        @input="value => updateData('platform', value)"
                        :items="platforms"
                        label="Platform"
                        disabled
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="6" v-show="platform === 'desktop'">
                        <v-select
                        :value="type"
                        @input="value => updateData('type', value)"
                        :items="types"
                        label="type"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="6" v-show="platform === 'mobile'">
                        <v-text-field
                        :value="category_id"
                        @input="value => updateData('category_id', value)"
                        label="Category ID"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                        :value="name"
                        @input="value => updateData('name', value)"
                        label="Category Name"
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
                @click="editCategoryModal = false"
            >
                Close
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
                color="primary"
                x-large
                 @click="submit"
            >
                Save
            </v-btn>
            <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { mdiCogOutline } from '@mdi/js';


export default {
    props: {
        id: Number,
        category_id: Number,
        isActive: String,
        platform: String,
        type: String,
        name: String,
    },
    data() {
        return {
            types: ['makanan', 'minuman', 'non-konsumsi'],
            platforms: ['desktop', 'mobile'],

            category: {
                id: null,
                platform: null,
                category_id: null,
                name: null,
                type: null,
                isActive: null,
            },

            editCategoryModal: false,
            icons:{
                mdiCogOutline,
            }

        }
    },
    methods: {
        getData(){
            //
        },
        initData(){
            this.category.id = this.id;
            this.category.category_id = this.category_id;
            this.category.isActive = this.isActive;
            this.category.platform = this.platform;
            this.category.type = this.type;
            this.category.name = this.name;
        },
        updateData(field, value) {
            this.category[field] = value;
        },
        submit(){
            //
            const formData = new FormData();
            formData.append("id", this.category.id);
            formData.append("category_id", this.category.category_id);
            formData.append("isActive", this.category.isActive);
            formData.append("platform", this.category.platform);
            formData.append("type", this.category.type);
            formData.append("name", this.category.name);
            axios.post('update-category', formData)
            .then((response) => {
                //
                Vue.$toast.open({
                    message: "category updated successfuly(local)",
                    type: 'success',
                    position: 'top',
                    duration: 2000,
                });
            }).catch((error) => {
                //
            });

            if (this.category.platform === 'desktop') {
                axios.post('http://localhost:8099/api/update-category', formData)
                .then((response) => {
                    //
                    Vue.$toast.open({
                        message: "category updated successfuly",
                        type: 'success',
                        position: 'top',
                        duration: 2000,
                    });

                    this.editCategoryModal = false;

                }).catch((error) => {
                    //
                    Vue.$toast.open({
                        message: "updating category failed",
                        type: 'error',
                        position: 'top',
                        duration: 2000,
                    });
                });
            } else {
                //
                Vue.$toast.open({
                    message: "mobile feature not ready yet",
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });

                this.editCategoryModal = false;
            }

        },
    },
    computed: {
        //
    },
    created() {
        //
    },
    beforeUpdate() {
        this.initData();
    }
}
</script>