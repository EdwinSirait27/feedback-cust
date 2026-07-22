import { ref, watchEffect } from 'vue'
import { useI18n } from 'vue-i18n'
import countries from 'i18n-iso-countries'

// Map locale aplikasi kamu ke locale yang didukung i18n-iso-countries
const LOCALE_MAP: Record<string, string> = {
    id: 'id',
    en: 'en',
    zh: 'zh',
    ja: 'ja',
    ko: 'ko',
}

const loadedLocales = new Set<string>()

async function ensureLocaleLoaded(locale: string) {
    if (loadedLocales.has(locale)) return

    const langData = await import(`i18n-iso-countries/langs/${locale}.json`)
    countries.registerLocale(langData.default ?? langData)
    loadedLocales.add(locale)
}

export function useCountries() {
    const { locale } = useI18n()
    const countryList = ref<{ code: string; name: string }[]>([])

    watchEffect(async () => {
        const isoLocale = LOCALE_MAP[locale.value] ?? 'en'

        await ensureLocaleLoaded(isoLocale)
        await ensureLocaleLoaded('en') 

        const names = countries.getNames(isoLocale, { select: 'official' })

        countryList.value = Object.entries(names)
            .map(([code, name]) => ({ code, name }))
            .sort((a, b) => a.name.localeCompare(b.name, isoLocale))
    })

    return { countries: countryList }
}