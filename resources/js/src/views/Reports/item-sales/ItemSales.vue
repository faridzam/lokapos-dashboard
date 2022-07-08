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
        <ItemSalesDesktop></ItemSalesDesktop>
      </v-tab-item>

      <v-tab-item>
        <ItemSalesMobile></ItemSalesMobile>
      </v-tab-item>

    </v-tabs-items>

</div>
</template>

<script>
import { mdiCellphone, mdiMonitor } from '@mdi/js';

//component
import ItemSalesMobile from './platform/ItemSalesMobile.vue';
import ItemSalesDesktop from './platform/ItemSalesDesktop.vue';

export default {
    components:{
        ItemSalesMobile,
        ItemSalesDesktop,
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