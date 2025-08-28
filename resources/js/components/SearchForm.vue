<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div class="flex flex-col md:flex-row gap-3">
      <!-- Search Input -->
      <div class="flex-1">
        <div class="relative">
          <Input 
            v-model="form.query" 
            placeholder="Search services or providers..." 
            class="pl-10 pr-4"
          />
          <Icon 
            name="Search" 
            class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-tg-hint" 
          />
        </div>
      </div>
      
      <!-- Sort Dropdown -->
      <div class="w-full md:w-48">
        <Select v-model="form.sort">
          <SelectTrigger>
            <SelectValue placeholder="Sort by" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="relevance">Relevance</SelectItem>
            <SelectItem value="price_low">Price: Low to High</SelectItem>
            <SelectItem value="price_high">Price: High to Low</SelectItem>
            <SelectItem value="rating">Rating</SelectItem>
          </SelectContent>
        </Select>
      </div>
      
      <!-- Search Button -->
      <Button type="submit" class="w-full md:w-auto">
        <Icon name="Search" class="mr-2 h-4 w-4" />
        Search
      </Button>
    </div>
    
    <!-- Additional Filters (Optional) -->
    <div class="flex flex-wrap gap-2">
      <Badge 
        v-for="category in popularCategories" 
        :key="category"
        :variant="form.category === category ? 'default' : 'outline'"
        class="cursor-pointer"
        @click="selectCategory(category)"
      >
        {{ category }}
      </Badge>
      <Badge 
        v-if="form.category && !popularCategories.includes(form.category)"
        variant="default"
        class="cursor-pointer"
        @click="form.category = ''"
      >
        {{ form.category }} ×
      </Badge>
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  Input, 
  Button, 
  Badge,
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui'
import Icon from '@/components/Icon.vue'

interface Props {
  initialQuery?: string
  initialCategory?: string
  initialSort?: string
}

const props = withDefaults(defineProps<Props>(), {
  initialQuery: '',
  initialCategory: '',
  initialSort: 'relevance'
})

const form = ref({
  query: props.initialQuery,
  category: props.initialCategory,
  sort: props.initialSort
})

// Example categories - these would typically come from your backend
const popularCategories = [
  'Haircut',
  'Massage',
  'Nails',
  'Fitness',
  'Consulting'
]

function selectCategory(category: string) {
  form.value.category = form.value.category === category ? '' : category
}

function handleSubmit() {
  // Use Inertia router to submit the search form
  router.get('/search', {
    query: form.value.query,
    category: form.value.category,
    sort: form.value.sort
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['services', 'providers', 'filters']
  })
}
</script>
