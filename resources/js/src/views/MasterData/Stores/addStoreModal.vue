<template>
    <v-dialog
    v-model="addStoreModal"
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
                <v-icon>{{icons.mdiStorePlus}}</v-icon>
                &nbsp; Add Store
            </v-btn>

        </template>
        <v-card>
            <v-card-title>
            <span class="text-h5">Add Store</span>
            </v-card-title>
            <v-card-text>
            <v-container>
                <v-row>
                    <v-col cols="12">
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
                    <v-col cols="6" v-if="selectedPlatform === 1">
                        <v-select
                        v-model="selectedType"
                        :items="desktopTypes"
                        item-text="name"
                        item-value="value"
                        label="Store Type"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="6" v-if="selectedPlatform != 1">
                        <v-select
                        v-model="selectedType"
                        :items="types"
                        item-text="name"
                        item-value="value"
                        label="Store Type"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="6">
                        <v-select
                        v-model="selectedArea"
                        :items="areas"
                        item-text="name"
                        item-value="value"
                        label="Store Area"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                            v-model="storeName"
                            label="Store Name"
                            placeholder="Foodtruck Ararya"
                            required
                            outlined
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" v-show="selectedPlatform === 2">
                        <v-text-field
                            v-model="MACMobile"
                            label="MAC Address Printer"
                            placeholder="00:00:0A:04:90:3A"
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
                @click="addStoreModal = false"
            >
                Close
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
                color="primary"
                x-large
                @click="addStore"
            >
                Add Store
            </v-btn>
            <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { mdiStorePlus } from '@mdi/js';


export default {
    data() {
        return {
            platforms: [{name: "Desktop", value: 1}, {name: "Mobile", value: 2}],
            selectedPlatform: null,
            types: [{name: "FnB", value: 1}, {name: "Retail", value: 2}, {name: "Rental", value: 4}, {name: "Others", value: 3}],
            desktopTypes: [{name: "FnB", value: 1}, {name: "Retail", value: 2}, {name: "Others", value: 3}],
            selectedType: null,
            areas: [{name: "Downtown", value: 1}, {name: "Pesisir", value: 2}, {name: "Balalantara", value: 3}, {name: "Kamayayi", value: 4}, {name: "Ararya", value: 5}, {name: "Segara Prada", value: 6}, {name: "Others", value: 7}],
            selectedArea: null,
            addStoreModal: false,
            storeName: '',
            MACMobile: '',
            icons:{
                mdiStorePlus,
            }
        }
    },
    methods: {
        addStore(){
            //
            if (this.selectedPlatform === 1) {
                //
                const formData = new FormData();
                formData.append("name", this.storeName);
                formData.append("type", this.selectedType);
                formData.append("area", this.selectedArea);
                axios.post('http://localhost:8099/api/add-store', formData)
                .then((response) => {
                    //
                    const formData = new FormData();
                    formData.append("store_id", response.data.id);
                    formData.append("name", this.storeName);
                    formData.append("type", this.selectedType);
                    formData.append("area", this.selectedArea);
                    formData.append("platform", this.selectedPlatform);
                    axios.post('add-store-desktop', formData)
                    .then((response) => {
                        //
                        Vue.$toast.open({
                            message: "store added successfuly",
                            type: 'success',
                            position: 'top',
                            duration: 2000,
                        });
                        this.refreshData();
                        this.addStoreModal = false;
                    }).catch((error) => {
                        //
                        Vue.$toast.open({
                            message: error,
                            type: 'error',
                            position: 'top',
                            duration: 2000,
                        });
                    })
                }).catch((error) => {
                    //
                    Vue.$toast.open({
                        message: error,
                        type: 'error',
                        position: 'top',
                        duration: 2000,
                    });
                })
            } else if (this.selectedPlatform === 2) {
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