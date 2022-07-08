<template>
    <v-dialog
    v-model="editStoreModal"
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
                    <v-col cols="12">
                        <v-select
                        :value="platform"
                        @input="value => updateStore('platform', value)"
                        :items="platforms"
                        label="Platform"
                        disabled
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="6">
                        <v-select
                        :value="type"
                        @input="value => updateStore('type', value)"
                        :items="types"
                        label="type"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="6">
                        <v-select
                        :value="area"
                        @input="value => updateStore('area', value)"
                        :items="areas"
                        label="area"
                        required
                        outlined>
                        </v-select>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field
                        :value="name"
                        @input="value => updateStore('name', value)"
                        label="Store Name"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-if="platform === 'mobile'"
                        :value="ip_address_mobile"
                        @input="value => updateStore('ip_address_mobile', value)"
                        label="Mac Address"
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
                @click="editStoreModal = false"
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
        store_id: Number,
        isActive: String,
        platform: String,
        type: String,
        area: String,
        name: String,
        ip_address_mobile: String,
    },
    data() {
        return {
            types: ['fnb', 'retail', 'others', 'rental'],
            desktopTypes: ['fnb', 'retail', 'others'],
            areas: ['downtown', 'pesisir', 'balalantara', 'kamayayi', 'ararya', 'segara prada', 'others'],
            platforms: ['desktop', 'mobile'],

            store: {
                platform: null,
                store_id: null,
                name: null,
                ip_address_mobile: null,
                type: null,
                area: null,
                isActive: null,
            },

            editStoreModal: false,
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
            this.store.id = this.id;
            this.store.store_id = this.store_id;
            this.store.isActive = this.isActive;
            this.store.platform = this.platform;
            this.store.type = this.type;
            this.store.area = this.area;
            this.store.name = this.name;
            this.store.ip_address_mobile = this.ip_address_mobile;
        },
        updateStore(field, value) {
            this.store[field] = value;
        },
        submit(){
            const formData = new FormData();
            formData.append("id", this.store.id);
            formData.append("store_id", this.store.store_id);
            formData.append("isActive", this.store.isActive);
            formData.append("platform", this.store.platform);
            formData.append("type", this.store.type);
            formData.append("area", this.store.area);
            formData.append("name", this.store.name);
            formData.append("ip_address_mobile", this.store.ip_address_mobile);
            axios.post('update-store', formData)
            .then((response) => {
                //
            }).catch((error) => {
                //
                Vue.$toast.open({
                    message: "updating store failed",
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });
            });

            if (this.store.platform === 'desktop') {
                axios.post('http://localhost:8099/api/update-store', formData)
                .then((response) => {
                    //
                    Vue.$toast.open({
                        message: "store updated successfuly",
                        type: 'success',
                        position: 'top',
                        duration: 2000,
                    });

                    this.editStoreModal = false;

                }).catch((error) => {
                    //
                    Vue.$toast.open({
                        message: "updating store failed",
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
            }
        },
    },
    computed: {
        // dialog: {
        //     get () { return this.value; },
        //     set (value) { this.$emit('close', value); }
        // }
    },
    created() {
        //
    },
    beforeUpdate () {
        this.initData();
    }
}
</script>