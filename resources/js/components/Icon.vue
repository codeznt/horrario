<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

// Selective imports - only the icons we actually use (reduces bundle by ~600KB)
import { 
  AlertCircle,
  AlertTriangle, 
  ArrowLeft,
  BarChart3,
  Building2,
  Calendar,
  Check,
  CheckCircle,
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  Clock,
  DollarSign,
  Edit,
  Eye,
  FileText,
  Heart,
  HelpCircle,
  History,
  Home,
  Info,
  Layout,
  Loader2,
  Mail,
  MapPin,
  MessageCircle,
  MessageSquare,
  MoreVertical,
  Package,
  Phone,
  Play,
  Plus,
  Save,
  Search,
  Settings,
  Sparkles,
  Store,
  Timer,
  Trash2,
  TrendingUp,
  Upload,
  User,
  UserCheck,
  UserPlus,
  X,
  XCircle
} from 'lucide-vue-next';

interface Props {
    name: string;
    class?: string;
    size?: number | string;
    color?: string;
    strokeWidth?: number | string;
}

const props = withDefaults(defineProps<Props>(), {
    class: '',
    size: 16,
    strokeWidth: 2,
});

const className = computed(() => cn('h-4 w-4', props.class));

// Icon mapping for selective imports - dramatically reduces bundle size
const iconMap = {
  AlertCircle,
  AlertTriangle,
  ArrowLeft,
  BarChart3,
  Building2,
  Calendar,
  Check,
  CheckCircle,
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  Clock,
  DollarSign,
  Edit,
  Eye,
  FileText,
  Heart,
  HelpCircle,
  History,
  Home,
  Info,
  Layout,
  Loader2,
  Mail,
  MapPin,
  MessageCircle,
  MessageSquare,
  MoreVertical,
  Package,
  Phone,
  Play,
  Plus,
  Save,
  Search,
  Settings,
  Sparkles,
  Store,
  Timer,
  Trash2,
  TrendingUp,
  Upload,
  User,
  UserCheck,
  UserPlus,
  X,
  XCircle,
} as const;

const icon = computed(() => {
  const iconName = props.name as keyof typeof iconMap;
  const IconComponent = iconMap[iconName];
  
  if (!IconComponent) {
    console.warn(`Icon "${props.name}" not found. Available icons:`, Object.keys(iconMap));
    return iconMap.AlertCircle; // fallback
  }
  
  return IconComponent;
});
</script>

<template>
    <component 
      :is="icon" 
      :class="className" 
      :size="size" 
      :stroke-width="strokeWidth" 
      :color="color" 
      aria-hidden="true"
    />
</template>
