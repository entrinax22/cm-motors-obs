<template>
    <div class="w-full overflow-x-auto rounded-xl bg-blue-900 shadow-lg">
        <table class="w-full table-auto divide-y divide-blue-700">
            <thead class="bg-blue-800">
                <tr>
                    <!-- Row # -->
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-200 uppercase">#</th>
                    <th
                        v-for="col in props.columns"
                        :key="col.key"
                        :class="[
                            'px-6 py-3 text-xs font-medium tracking-wider text-gray-200 uppercase',
                            col.class || '',
                            col.align ? `text-${col.align}` : 'text-left',
                        ]"
                    >
                        {{ col.label }}
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-blue-700 text-gray-100">
                <tr v-for="(row, index) in props.data" :key="row.id" class="transition hover:bg-blue-800/50">
                    <!-- Auto row number -->
                    <td class="px-6 py-4 text-left whitespace-nowrap">
                        {{ (props.pagination ? props.pagination.from - 1 : 0) + index + 1 }}
                    </td>

                    <td
                        v-for="col in props.columns"
                        :key="col.key"
                        :class="['px-6 py-4 whitespace-nowrap', col.class || '', col.align ? `text-${col.align}` : 'text-left']"
                    >
                        <slot :name="col.key" :row="row">
                            <template v-if="col.key === 'profile_picture' && row.profile_picture">
                                <img :src="row.profile_picture" alt="Profile" class="h-8 w-8 rounded-full object-cover" />
                            </template>
                            <template v-else-if="col.key === 'is_admin'">
                                <span :class="row.is_admin ? 'font-bold text-cyan-400' : 'text-gray-400'">
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

        <!-- Pagination -->
        <div
            v-if="props.pagination"
            class="flex flex-col gap-2 border-t border-blue-700 bg-blue-800 px-6 py-4 text-gray-200 md:flex-row md:items-center md:justify-between"
        >
            <div class="text-sm">Showing page {{ props.pagination.from }} to {{ props.pagination.to }} of {{ props.pagination.total }} users</div>
            <div class="flex gap-2">
                <button
                    :disabled="props.pagination.current_page === 1"
                    @click="$emit('page-change', props.pagination.current_page - 1)"
                    class="rounded bg-blue-700 px-3 py-1 text-gray-200 transition hover:bg-cyan-600 disabled:opacity-50"
                >
                    Prev
                </button>
                <span class="px-2">Page {{ props.pagination.current_page }} of {{ props.pagination.last_page }}</span>
                <button
                    :disabled="props.pagination.current_page === props.pagination.last_page"
                    @click="$emit('page-change', props.pagination.current_page + 1)"
                    class="rounded bg-blue-700 px-3 py-1 text-gray-200 transition hover:bg-cyan-600 disabled:opacity-50"
                >
                    Next
                </button>
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
/* Rounded corners for first and last table cells */
table th:first-child,
table td:first-child {
    border-top-left-radius: 0.5rem;
    border-bottom-left-radius: 0.5rem;
}
table th:last-child,
table td:last-child {
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

/* Small screens: wrap text */
@media (max-width: 768px) {
    table th,
    table td {
        white-space: normal;
        word-break: break-word;
    }
}
</style>
