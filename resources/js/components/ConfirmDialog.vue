<template>
  <AlertDialog :open="show" @update:open="handleOpenChange">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle class="flex items-center gap-2">
          <Icon v-if="variant === 'destructive'" name="AlertTriangle" class="h-5 w-5 text-red-500" />
          <Icon v-else-if="variant === 'warning'" name="AlertCircle" class="h-5 w-5 text-yellow-500" />
          <Icon v-else name="HelpCircle" class="h-5 w-5 text-blue-500" />
          {{ title }}
        </AlertDialogTitle>
        <AlertDialogDescription>
          {{ message }}
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="onCancel">{{ cancelText }}</AlertDialogCancel>
        <AlertDialogAction 
          @click="onConfirm" 
          :class="actionButtonClass"
        >
          {{ confirmText }}
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { 
  AlertDialog, 
  AlertDialogAction, 
  AlertDialogCancel, 
  AlertDialogContent, 
  AlertDialogDescription, 
  AlertDialogFooter, 
  AlertDialogHeader, 
  AlertDialogTitle 
} from '@/components/ui'
import Icon from '@/components/Icon.vue'

interface Props {
  show?: boolean
  title?: string
  message?: string
  confirmText?: string
  cancelText?: string
  variant?: 'default' | 'destructive' | 'warning'
}

const props = withDefaults(defineProps<Props>(), {
  show: false,
  title: 'Confirm Action',
  message: 'Are you sure you want to proceed?',
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  variant: 'default',
})

const emit = defineEmits<{
  confirm: []
  cancel: []
  'update:show': [value: boolean]
}>()

const actionButtonClass = computed(() => {
  switch (props.variant) {
    case 'destructive':
      return 'bg-red-600 hover:bg-red-700 text-white'
    case 'warning':
      return 'bg-yellow-600 hover:bg-yellow-700 text-white'
    default:
      return ''
  }
})

function onConfirm() {
  emit('confirm')
  emit('update:show', false)
}

function onCancel() {
  emit('cancel')
  emit('update:show', false)
}

function handleOpenChange(open: boolean) {
  if (!open) {
    emit('cancel')
  }
  emit('update:show', open)
}
</script>
