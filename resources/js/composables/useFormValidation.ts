import { computed, ref, type Ref } from 'vue';

export interface ValidationRule {
    required?: boolean | string;
    minLength?: number;
    maxLength?: number;
    pattern?: RegExp;
    patternMessage?: string;
    custom?: (value: any, form: Record<string, any>) => string | null;
}

export interface ValidationRules {
    [key: string]: ValidationRule;
}

export interface FormValidationReturn<T> {
    form: Ref<T>;
    errors: Ref<Record<string, string>>;
    processing: Ref<boolean>;
    resetForm: () => void;
    validate: (rules: ValidationRules) => boolean;
    hasErrors: Ref<boolean>;
    setError: (field: string, message: string) => void;
    clearError: (field: string) => void;
    clearAllErrors: () => void;
}

export function useFormValidation<T extends Record<string, any>>(initialValues: T): FormValidationReturn<T> {
    const form = ref({ ...initialValues }) as Ref<T>;
    const errors = ref<Record<string, string>>({});
    const processing = ref(false);

    const resetForm = () => {
        form.value = { ...initialValues };
        errors.value = {};
    };

    const setError = (field: string, message: string) => {
        errors.value[field] = message;
    };

    const clearError = (field: string) => {
        delete errors.value[field];
    };

    const clearAllErrors = () => {
        errors.value = {};
    };

    const validate = (rules: ValidationRules): boolean => {
        const newErrors: Record<string, string> = {};

        Object.keys(rules).forEach((field) => {
            const fieldRules = rules[field];
            const value = form.value[field];

            // Required validation
            if (fieldRules.required && (!value || value === '')) {
                newErrors[field] = typeof fieldRules.required === 'string' ? fieldRules.required : `${field} is required`;
                return;
            }

            // Skip other validations if field is empty and not required
            if (!value || value === '') {
                return;
            }

            // Min length validation
            if (fieldRules.minLength && value.length < fieldRules.minLength) {
                newErrors[field] = `${field} must be at least ${fieldRules.minLength} characters`;
                return;
            }

            // Max length validation
            if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
                newErrors[field] = `${field} must be less than ${fieldRules.maxLength} characters`;
                return;
            }

            // Pattern validation
            if (fieldRules.pattern && !fieldRules.pattern.test(value)) {
                newErrors[field] = fieldRules.patternMessage || `${field} format is invalid`;
                return;
            }

            // Custom validation
            if (fieldRules.custom && typeof fieldRules.custom === 'function') {
                const customError = fieldRules.custom(value, form.value);
                if (customError) {
                    newErrors[field] = customError;
                    return;
                }
            }
        });

        errors.value = newErrors;
        return Object.keys(newErrors).length === 0;
    };

    const hasErrors = computed(() => Object.keys(errors.value).length > 0);

    return {
        form,
        errors,
        processing,
        resetForm,
        validate,
        hasErrors,
        setError,
        clearError,
        clearAllErrors,
    };
}
