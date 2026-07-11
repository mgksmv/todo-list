// @ts-check
import withNuxt from './.nuxt/eslint.config.mjs';

export default withNuxt({
  rules: {
    quotes: ['error', 'single'],
    semi: ['error', 'always'],
    indent: ['error', 2, { SwitchCase: 1 }],
    'comma-dangle': ['error', 'always'],
    'vue/multi-word-component-names': 'off',
    '@typescript-eslint/no-explicit-any': ['off'],
    'vue/html-self-closing': [
      'error',
      {
        html: {
          void: 'always',
        },
      },
    ],
  },
  languageOptions: {
    ecmaVersion: 'latest',
    ecmaFeatures: {
      jsx: false,
    },
  },
});
