<template>
    <v-card>

        <v-data-table
            style="white-space: pre;"
            :headers="headers"
            :items="items"
            :search="search"
            :loading="loading"
            class="elevation-1"
        >
            <template v-slot:item="row">
                <tr>
                    <td>{{row.item.index}}</td>
                    <td>{{row.item.category_id}}</td>
                    <td>{{row.item.name}}</td>
                    <td>{{row.item.type}}</td>
                    <td>{{row.item.platform}}</td>
                    <td>{{row.item.isActive}}</td>
                    <td>
                         <editCategoryModal :id="row.item.id" :category_id="row.item.category_id" :name="row.item.name" :type="row.item.type" :platform="row.item.platform" :isActive="row.item.isActive"></editCategoryModal>
                    </td>
                </tr>
            </template>

            <template v-slot:top>
                <v-row class="mx-5">

                    <v-col cols="8">
                        <v-text-field
                        v-model="search"
                        label="Search"
                        class="mx-5"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="4">

                        <v-btn class="mx-2 mt-3" color="primary" outlined>
                            <v-icon>{{icons.mdiReload}}</v-icon>
                            &nbsp; Sync
                        </v-btn>

                        <addCategoryModal @getData="getData"></addCategoryModal>

                    </v-col>

                </v-row>
            </template>

        </v-data-table>

    </v-card>
</template>

<script>
import { mdiReload, mdiCogOutline } from '@mdi/js';
import addCategoryModal from './addCategoryModal.vue';
import editCategoryModal from './editCategoryModal.vue';

export default {
    components:{
        addCategoryModal,
        editCategoryModal
    },
    data() {
        return {
            search: '',
            loading: false,
            stores: [],
            headers: [
                {
                    text: '#',
                    align: 'start',
                    filterable: true,
                    value: 'index',
                },
                { text: 'Category ID', value: 'category_id'},
                { text: 'Category Name', value: 'name'},
                { text: 'Category Type', value: 'type'},
                { text: 'Platform', value: 'platform'},
                { text: 'Status', value: 'isActive'},
                { text: 'Actions', value: 'actions'},
            ],
            items: [
                {
                    //
                }
            ],
            icons:{
                mdiReload,
                mdiCogOutline,
            }
        }
    },
    methods: {
        //
        getData(){
            //
            this.loading = true;
            axios.get('api/get-data-categories')
            .then((response) => {
                let Obj = response.data.categories;
                var result=[];
                for(var i=0;i<Obj.length;i++){
                    result.push({index: i+1, id: Obj[i].id, category_id: Obj[i].category_id, name: Obj[i].name, type: Obj[i].type, platform: Obj[i].platform, isActive: Obj[i].isActive,});
                }
                this.items = result;
                this.loading = false;
            }).catch((error) => {
                //
                console.log(error);
                this.loading = false;
            });
        }
    },
    mounted() {
        //
        this.getData();
    },
}
</script>