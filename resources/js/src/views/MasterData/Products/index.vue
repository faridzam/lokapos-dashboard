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
            <!-- <template v-slot:item="row">
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
            </template> -->

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

                    </v-col>

                </v-row>
            </template>

        </v-data-table>

    </v-card>
</template>

<script>
import { mdiReload, mdiCogOutline } from '@mdi/js';
export default {
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
                { text: 'Product ID', value: 'product_id'},
                { text: 'Product Code', value: 'product_code'},
                { text: 'Product Name', value: 'name'},
                { text: 'cost', value: 'cost'},
                { text: 'price', value: 'price'},
                { text: 'isActive', value: 'isActive'},
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
}
</script>