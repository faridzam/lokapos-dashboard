<template>
<div>
    <v-card class="mx-5 px-3 pt-5 mb-5 mt-5 pt-3">
        <v-card-text style="font-size: 20px; font-weight: 600; margin: 0 0 10px 0; padding: 0;">Store:</v-card-text>
        <v-select v-model="selectedStore" placeholder="store" :items="stores" item-text="name" item-value="id" label="Store" v-on:change="onChangeInput" outlined></v-select>
    </v-card>

<v-row class="mb-5">
<v-col cols="6">

    <v-card class="mx-5 px-3 pt-5 mb-5 mt-5 pt-3">
    <v-card-text style="font-size: 20px; font-weight: 600; margin: 5px 0 20px 0; padding: 0;">Date Start:</v-card-text>
        <v-menu
            v-model="menuFrom"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
        >
            <template v-slot:activator="{ on, attrs }">
            <v-text-field
                v-model="dateFrom"
                label="Date Start"
                readonly
                outlined
                v-bind="attrs"
                v-on="on"
            ></v-text-field>
            </template>
            <v-date-picker
            v-model="dateFrom"
            @change="onChangeInput"
            @input="menuFrom = false && onChangeInput"
            ></v-date-picker>
        </v-menu>

    </v-card>

</v-col>
<v-col cols="6">

    <v-card class="mx-5 px-3 pt-5 mb-5 mt-5 pt-3">
    <v-card-text style="font-size: 20px; font-weight: 600; margin: 5px 0 20px 0; padding: 0;">Date End:</v-card-text>
        <v-menu
            v-model="menuTo"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
        >
            <template v-slot:activator="{ on, attrs }">
            <v-text-field
                v-model="dateTo"
                label="Date End"
                readonly
                outlined
                v-bind="attrs"
                v-on="on"
            ></v-text-field>
            </template>
            <v-date-picker
            v-model="dateTo"
            @change="onChangeInput"
            @input="menuTo = false && onChangeInput"
            ></v-date-picker>
        </v-menu>
    </v-card>
</v-col>
</v-row>
    <v-card>

        <v-data-table
            style="white-space: pre;"
            :headers="headers"
            :items="items"
            :search="search"
            :loading="loading"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-row class="mx-5">
                    <v-col cols="9">
                        <v-text-field
                        v-model="search"
                        label="Search"
                        class="mx-5"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                        <v-btn class="mx-2 mt-3" color="primary" outlined @click="syncSalesOrder">
                            <v-icon>{{icons.mdiReload}}</v-icon>
                            &nbsp; sync
                        </v-btn>
                        <v-btn
                        @click="exportItemSales"
                        color="primary"
                        class="mx-2 mt-3">
                            Export
                        </v-btn>
                    </v-col>
                </v-row>
            </template>

        </v-data-table>

    </v-card>

</div>
</template>

<script>
import { mdiReload } from '@mdi/js'

export default {

    data() {
        return {
            search: '',
            loading: false,
            dateFrom: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
            dateTo: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
            menuFrom: false,
            menuTo: false,
            stores: [],
            selectedStore: {},
            existOrderId: [],
            headers: [
                {
                    text: '#',
                    align: 'start',
                    filterable: true,
                    value: 'index',
                },
                { text: 'Product Code', value: 'product_code'},
                { text: 'Product Name', value: 'name'},
                { text: 'Price', value: 'price'},
                { text: 'Quantity', value: 'qty'},
                { text: 'Subtotal', value: 'subtotal'},
            ],
            items: [
                {
                    //
                }
            ],
            icons:{
                mdiReload,
            }
        }
    },
    methods: {
        //
        getStore(){
            axios.get('/api/get-stores')
            .then((response)=>{
                this.stores = response.data.stores;
                //
            }).catch((error) =>{
                //
            });
        },
        onChangeInput(){
            //
            this.loading = true;
            axios.get('api/get-item-sales-order/'+this.selectedStore+'/'+this.dateFrom+'/'+this.dateTo)
            .then((response) => {
                let itemTemp = response.data.order;

                let Obj = itemTemp;
                var result=[];
                for(var i=0;i<Obj.length;i++){
                    result.push({index: i+1, product_code: Obj[i].product_code, name: Obj[i].name, price: "Rp. "+Obj[i].price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","), qty: Obj[i].qty, subtotal: "Rp. "+Obj[i].subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")});
                }
                this.items = result;

                // axios.post('getOrderData')
                // .then((response) => {
                //     //
                // }).catch((error) => {
                //     //
                // });
                this.loading = false;
            }).catch((error) => {
                //
                this.loading = false;
            });
        },
        syncSalesOrder(){
            //
            this.loading = true;
            axios.get('api/get-exist-order-id')
            .then((response) => {
                
                const formData = new FormData();
                formData.append("existId", response.data.existOrderId);
                axios.post('http://localhost:8099/api/sync-order', formData)
                .then((response) => {
                    //
                    const formData = new FormData();
                    formData.append("order", JSON.stringify(response.data.order));
                    axios.post('fetch-order', formData)
                    .then((response) => {

                        Vue.$toast.open({
                            message: response.data.message,
                            type: 'success',
                            position: 'top',
                            duration: 2000,
                        });

                    }).catch((error) => {
                        //
                        Vue.$toast.open({
                            message: error,
                            type: 'error',
                            position: 'top',
                            duration: 2000,
                        });

                    });
                }).catch((error) => {
                    //
                    Vue.$toast.open({
                        message: error,
                        type: 'error',
                        position: 'top',
                        duration: 2000,
                    });
                });
                this.loading = false;
            }).catch((error) => {

                Vue.$toast.open({
                    message: error,
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });

                this.loading = false;
            });
        },
        exportItemSales(){
            //
            window.open('api/export-item-sales/'+this.selectedStore+'/'+this.dateFrom+'/'+this.dateTo)
            .then((response) => {
                //
                window.close();
            }).catch((error) => {
                //
                window.close();
            })
        },
    },
    mounted() {
        //
        this.getStore();
        this.syncSalesOrder();
    },
    computed:{
        // options(){
        //     let Obj = this.stores;
        //     var result=[];
        //     for(var i=0;i<Obj.length;i++){
        //         result.push({id: Obj[i].id, name: Obj[i].name, isActive: Obj[i].isActive});
        //     }
        //     return result;
        // }
    },
}
</script>