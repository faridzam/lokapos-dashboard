<template>
<div>
    <!-- tabs -->
    <v-tabs v-model="tab" show-arrows grow>
      <v-tab v-for="tab in tabs" :key="tab.icon">
        <v-icon size="20" class="me-3">
          {{ tab.icon }}
        </v-icon>
        <span>{{ tab.title }}</span>
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">

      <v-tab-item>
        <SalesDesktop></SalesDesktop>
      </v-tab-item>

      <v-tab-item>
        <SalesMobile></SalesMobile>
      </v-tab-item>

    </v-tabs-items>

</div>
</template>

<script>
import { mdiCellphone, mdiMonitor } from '@mdi/js';

//component
import SalesMobile from './platform/SalesMobile.vue';
import SalesDesktop from './platform/SalesDesktop.vue';

export default {
    components:{
        SalesMobile,
        SalesDesktop,
    },
    data() {
        return {
            tab: '',
            tabs: [
                {title: 'desktop', icon: mdiMonitor},
                {title: 'mobile', icon: mdiCellphone}
            ],
            
        }
    }
}
</script>