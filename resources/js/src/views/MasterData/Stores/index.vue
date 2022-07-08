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
                    <td>{{row.item.name}}</td>
                    <td>{{row.item.ip_address_mobile}}</td>
                    <td>{{row.item.type}}</td>
                    <td>{{row.item.area}}</td>
                    <td>{{row.item.platform}}</td>
                    <td>{{row.item.isActive}}</td>
                    <td>
                        <editStoreModal :id="row.item.id" :store_id="row.item.store_id" :name="row.item.name" :area="row.item.area" :ip_address_mobile="row.item.ip_address_mobile" :type="row.item.type" :platform="row.item.platform" :isActive="row.item.isActive"></editStoreModal>
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

                        <v-btn class="mx-2 mt-3" color="primary" outlined @click="syncStore">
                            <v-icon>{{icons.mdiReload}}</v-icon>
                            &nbsp; Sync
                        </v-btn>

                        <addStoreModal @getData="getData"></addStoreModal>

                    </v-col>

                </v-row>
            </template>

        </v-data-table>

    </v-card>
</template>

<script>
import { mdiReload, mdiCogOutline } from '@mdi/js';
import addStoreModal from './addStoreModal.vue';
import editStoreModal from './editStoreModal.vue';

export default {
    components:{
        addStoreModal,
        editStoreModal
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
                { text: 'Store Name', value: 'name'},
                { text: 'IP Address (Mobile)', value: 'ip_address_mobile'},
                { text: 'Store Type', value: 'type'},
                { text: 'Area', value: 'area'},
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
        getData(){
            //
            this.loading = true;
            axios.get('api/get-data-stores')
            .then((response) => {
                let Obj = response.data.stores;
                var result=[];

                for(var i=0;i<Obj.length;i++){
                    result.push({index: i+1, id: Obj[i].id, store_id: Obj[i].store_id ,name: Obj[i].name, ip_address_mobile: Obj[i].ip_address_mobile, type: Obj[i].type, area: Obj[i].area, platform: Obj[i].platform, isActive: Obj[i].isActive});
                }
                this.items = result;

                this.loading = false;
            }).catch((error) => {
                //
                console.log(error);
                this.loading = false;
            });
        },
        syncStore(){
            //
            this.loading = true;
            axios.get('api/get-exist-desktop-store-id')
            .then((response) => {
                //
                const formData = new FormData();
                formData.append("existId", response.data.storeId);

                axios.post('http://localhost:8099/api/sync-store', formData)
                .then((response) => {
                    const formData = new FormData();
                    formData.append("stores", JSON.stringify(response.data.newStore));
                    axios.post('fetch-store-desktop', formData)
                    .then((response) => {
                        //
                        Vue.$toast.open({
                            message: response.data.message,
                            type: 'success',
                            position: 'top',
                            duration: 2000,
                        });
                        this.loading = false;
                    }).catch((error) => {
                        //
                        Vue.$toast.open({
                            message: error,
                            type: 'error',
                            position: 'top',
                            duration: 2000,
                        });
                        this.loading = false;
                    });
                }).catch((error) => {
                    //
                    Vue.$toast.open({
                        message: error,
                        type: 'error',
                        position: 'top',
                        duration: 2000,
                    });
                    this.loading = false;
                })
            }).catch((error) => {
                //
                Vue.$toast.open({
                    message: error,
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });
                this.loading = false;
            })
        },
    },
    mounted() {
        //
        this.getData();
        this.syncStore();
    },
}
</script>