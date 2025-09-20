<template>
  <div class="overflow-x-auto bg-white rounded-xl shadow">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <!-- New # column -->
          <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-700 text-left">
            #
          </th>

          <th v-for="col in props.columns" :key="col.key"
              :class="['px-6 py-3 text-xs font-medium uppercase tracking-wider', col.class || '', col.align ? `text-${col.align}` : 'text-left', 'text-gray-700']">
            {{ col.label }}
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 text-gray-900">
        <tr v-for="(row, index) in props.data" :key="row.id">
          <!-- Auto row number -->
          <td class="px-6 py-4 whitespace-nowrap text-left">
            {{ (props.pagination ? (props.pagination.from - 1) : 0) + index + 1 }}
          </td>

          <td v-for="col in props.columns" :key="col.key"
              :class="['px-6 py-4 whitespace-nowrap', col.class || '', col.align ? `text-${col.align}` : 'text-left']">
            <slot :name="col.key" :row="row">
              <template v-if="col.key === 'profile_picture' && row.profile_picture">
                <img :src="row.profile_picture" alt="Profile" class="h-8 w-8 rounded-full object-cover" />
              </template>
              <template v-else-if="col.key === 'is_admin'">
                <span :class="row.is_admin ? 'text-green-600 font-bold' : 'text-gray-500'">
                  {{ row.is_admin ? 'Admin' : 'User' }}
                </span>
              </template>
              <template v-else>
                {{ row[col.key] }}
              </template>
            </slot>
          </td>
        </tr>
        <tr v-if="props.data.length === 0">
          <td :colspan="props.columns.length + 1" class="px-6 py-4 text-center text-gray-400">No data found.</td>
        </tr>
      </tbody>
    </table>
    <div v-if="props.pagination" class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-200">
      <div class="text-sm text-gray-600">
        Showing page {{ props.pagination.from }} to {{ props.pagination.to }} of {{ props.pagination.total }} users
      </div>
      <div class="flex gap-2">
        <button
          :disabled="props.pagination.current_page === 1"
          @click="$emit('page-change', props.pagination.current_page - 1)"
          class="px-3 py-1 rounded bg-gray-200 text-gray-700 disabled:opacity-50"
        >Prev</button>
        <span class="px-2 text-gray-600">Page {{ props.pagination.current_page }} of {{ props.pagination.last_page }}</span>
        <button
          :disabled="props.pagination.current_page === props.pagination.last_page"
          @click="$emit('page-change', props.pagination.current_page + 1)"
          class="px-3 py-1 rounded bg-gray-200 text-gray-700 disabled:opacity-50"
        >Next</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

interface Column {
  key: string;
  label: string;
  align?: string;
  class?: string;
}

const props = defineProps<{
  columns: Column[];
  data: any[];
  pagination?: {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
    from: number;
    to: number;
  };
}>();
</script>

<style scoped>
/* You can add table-specific styles here if needed */
</style>
