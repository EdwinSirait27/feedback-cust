import { createI18n } from 'vue-i18n'
import id from './lang/id.json'
import en from './lang/en.json'
import zh from './lang/zh.json'
import ja from './lang/ja.json'
import ko from './lang/ko.json'
import ru from './lang/ru.json'

export const i18n = createI18n({
    legacy: false, // wajib false supaya bisa pakai Composition API (useI18n)
    locale: 'en',
    fallbackLocale: 'en',
    messages: { id, en, zh, ja, ko, ru },
})