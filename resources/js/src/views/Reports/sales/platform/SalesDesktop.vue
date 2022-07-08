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
                        @click="exportSales"
                        color="primary"
                        class="mx-2 mt-3">
                            Export
                        </v-btn>
                    </v-col>
                </v-row>
            </template>

        </v-data-table>

        <div class="footer-datatable" style="display: flex; margin: 1rem 1rem 1rem 2rem;">
            <h1 style="margin: 0 1rem 0 0">
                Total: 
            </h1>
            <h1>
                Rp. {{this.omset.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}
            </h1>
        </div>

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
            omset: 0,
            selectedStore: {},
            existOrderId: [],
            headers: [
                {
                    text: '#',
                    align: 'start',
                    filterable: true,
                    value: 'index',
                },
                { text: 'Invoice Number', value: 'invoice'},
                { text: 'Cashier', value: 'cashier'},
                { text: 'Payment Method', value: 'payment'},
                { text: 'Items', value: 'items'},
                { text: 'Bill Amount', value: 'bill_amount'},
                { text: 'Date', value: 'date'},
                { text: 'Time', value: 'time'},
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
            }).catch((error) =>{
                console.log(error);
            });
        },
        onChangeInput(){
            //
            this.loading = true;
            axios.get('api/get-sales-order/'+this.selectedStore+'/'+this.dateFrom+'/'+this.dateTo)
            .then((response) => {
                let itemTemp = response.data.order;
                this.omset = response.data.omset;

                let Obj = itemTemp;
                var result=[];
                for(var i=0;i<Obj.length;i++){
                    let t= Obj[i].created_at.replace('.000000Z', '').replace(/T/g, ' ').split(/[- :]/);
                    var d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5])),
                    month = '' + (d.getMonth()),
                    day = '' + d.getDate(),
                    year = d.getFullYear();
                    result.push({index: i+1, invoice: Obj[i].no_invoice, cashier: Obj[i].cashier_id, payment: Obj[i].payment_id, items: Obj[i].items.replace(/,/g, '\n'), bill_amount: Obj[i].bill_amount, date: [day, month, year].join('-'), time: d.toLocaleTimeString()});
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
        exportSales(){
            //
            window.open('api/export-sales/'+this.selectedStore+'/'+this.dateFrom+'/'+this.dateTo)
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