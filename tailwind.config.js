import statamicPreset from './vendor/statamic/cms/tailwind.config.js'

export default {
    presets: [
        statamicPreset
    ],
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    safelist: [],
    theme: {
        extend: {},
    },
    corePlugins: {
        preflight: false,
        display: false,
    },
    important: false
}
