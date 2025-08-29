declare module '*.vue' {
    import { DefineComponent } from 'vue';
    // Use explicit object-like types to satisfy eslint rules
    const component: DefineComponent<Record<string, unknown>, Record<string, unknown>, any>;
    export default component;
}
