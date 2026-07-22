<!-- <script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { spin as spinAction, result as resultAction } from '@/actions/App/Http/Controllers/FeedbackController'

interface SpinResult {
    prize_type: string
    voucher_amount: number | null
    voucher_code: string | null
}

const props = defineProps<{
    alreadySpun: boolean
    prize: SpinResult | null
}>()

const { t } = useI18n()
const form = useForm({})
const wheelRotation = ref(0)
const spinning = ref(false)

const SEGMENTS = [
    { type: 'voucher_50k', label: 'Rp 50.000', color: '#C2185B', start: 0, end: 90 },
    { type: 'voucher_100k', label: 'Rp 100.000', color: '#D45D5D', start: 90, end: 180 },
    { type: 'no_prize', label: t('spin.thankYou'), color: '#2B2B2B', start: 180, end: 270 },
    { type: 'voucher_25k', label: 'Rp 25.000', color: '#1E5FA8', start: 270, end: 360 },
]

const gradient = computed(() =>
    `conic-gradient(${SEGMENTS.map((s) => `${s.color} ${s.start}deg ${s.end}deg`).join(', ')})`
)

function doSpin() {
    if (spinning.value || props.alreadySpun) return
    spinning.value = true
    form.submit(spinAction())
}

onMounted(() => {
    if (props.alreadySpun && props.prize) {
        animateTo(props.prize.prize_type)
    }
})

function animateTo(prizeType: string) {
    const segment = SEGMENTS.find((s) => s.type === prizeType) ?? SEGMENTS[2]
    const centerAngle = (segment.start + segment.end) / 2
    wheelRotation.value = 360 * 5 + (360 - centerAngle)

    setTimeout(() => {
        router.visit(resultAction())
    }, 4200)
}
</script>

<template>
    <div class="min-h-screen bg-[#FBF3E7] flex flex-col items-center justify-center px-6 py-10 gap-8">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-[#3B2A20]">{{ t('spin.title') }}</h1>
            <p class="text-sm text-[#8B5E3C] mt-1">{{ t('spin.subtitle') }}</p>
        </div>

        <div class="relative w-72 h-72">
            <div class="absolute left-1/2 -top-2 -translate-x-1/2 z-10 w-0 h-0
                        border-l-[12px] border-l-transparent border-r-[12px] border-r-transparent
                        border-t-[20px] border-t-[#3B2A20]" />

            <div
                class="w-72 h-72 rounded-full border-4 border-[#3B2A20] relative overflow-hidden transition-transform ease-out"
                :style="{
                    transform: `rotate(${wheelRotation}deg)`,
                    transitionDuration: spinning ? '4s' : '0s',
                    background: gradient,
                }"
            >
                <div
                    v-for="seg in SEGMENTS" :key="seg.type"
                    class="absolute inset-0 flex justify-center"
                    :style="{ transform: `rotate(${(seg.start + seg.end) / 2}deg)` }"
                >
                    <span
                        class="text-white text-[10px] font-bold mt-5 inline-block"
                        :style="{ transform: `rotate(${-(seg.start + seg.end) / 2}deg)` }"
                    >{{ seg.label }}</span>
                </div>
            </div>

            <button
                type="button"
                :disabled="spinning || alreadySpun"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-16 h-16 rounded-full
                       bg-[#3B2A20] text-white text-xs font-bold shadow-lg disabled:opacity-60 z-10"
                @click="doSpin"
            >{{ spinning ? '...' : t('spin.button') }}</button>
        </div>

        <p class="text-xs text-[#8B5E3C]">{{ t('spin.footerNote') }}</p>
    </div>
</template> -->
<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { spin as spinAction, result as resultAction } from '@/actions/App/Http/Controllers/FeedbackController'

interface SpinResult {
    prize_type: string
    voucher_amount: number | null
    voucher_code: string | null
}

const props = defineProps<{
    alreadySpun: boolean
    prize: SpinResult | null
}>()

const { t } = useI18n()
const form = useForm({})
const canvasRef = ref<HTMLCanvasElement | null>(null)
const spinning = ref(false)
const btnLabel = ref(t('spin.button'))
const spinTitle = ref(t('spin.title'))
const spinSubtitle = ref(t('spin.subtitle'))
const showConfetti = ref(false)

const SEGMENTS = [
    { type: 'voucher_50k',  label: 'Rp 50.000',  color: '#C2185B', textColor: '#fff' },
    { type: 'voucher_100k', label: 'Rp 100.000', color: '#D4A017', textColor: '#2C1A0E' },
    { type: 'no_prize',     label: t('spin.thankYou'), color: '#2B2B2B', textColor: '#888' },
    { type: 'voucher_25k',  label: 'Rp 25.000',  color: '#1E5FA8', textColor: '#fff' },
    { type: 'voucher_50k',  label: 'Rp 50.000',  color: '#C2185B', textColor: '#fff' },
    { type: 'voucher_100k', label: 'Rp 100.000', color: '#D4A017', textColor: '#2C1A0E' },
    { type: 'no_prize',     label: t('spin.thankYou'), color: '#2B2B2B', textColor: '#888' },
    { type: 'voucher_25k',  label: 'Rp 25.000',  color: '#1E5FA8', textColor: '#fff' },
]

let currentAngle = 0
let rafId: number | null = null

function drawWheel(angle: number) {
    const canvas = canvasRef.value
    if (!canvas) return
    const ctx = canvas.getContext('2d')!
    const cx = 134, cy = 134, r = 130
    const segAngle = (Math.PI * 2) / SEGMENTS.length
    ctx.clearRect(0, 0, 268, 268)

    SEGMENTS.forEach((seg, i) => {
        const start = angle + i * segAngle
        const end = start + segAngle

        ctx.beginPath()
        ctx.moveTo(cx, cy)
        ctx.arc(cx, cy, r, start, end)
        ctx.closePath()
        ctx.fillStyle = seg.color
        ctx.fill()
        ctx.strokeStyle = 'rgba(0,0,0,0.3)'
        ctx.lineWidth = 1
        ctx.stroke()

        ctx.save()
        ctx.translate(cx, cy)
        ctx.rotate(start + segAngle / 2)
        ctx.textAlign = 'right'
        ctx.fillStyle = seg.textColor
        ctx.font = 'bold 11px system-ui'
        ctx.fillText(seg.label, r - 12, 4)
        ctx.restore()
    })

    // Center cover
    ctx.beginPath()
    ctx.arc(cx, cy, 32, 0, Math.PI * 2)
    ctx.fillStyle = '#1a0f06'
    ctx.fill()
}

function ease(t: number) {
    return 1 - Math.pow(1 - t, 3)
}

function animateSpin(totalRotation: number, duration: number, onDone?: () => void) {
    const startTime = performance.now()
    const startAngle = currentAngle

    function frame(now: number) {
        const elapsed = now - startTime
        const progress = Math.min(elapsed / duration, 1)
        currentAngle = startAngle + totalRotation * ease(progress)
        drawWheel(currentAngle)

        if (progress < 1) {
            rafId = requestAnimationFrame(frame)
        } else {
            onDone?.()
        }
    }

    rafId = requestAnimationFrame(frame)
}

function getWinningSegment() {
    const segAngle = (Math.PI * 2) / SEGMENTS.length
    const normalized = ((-(currentAngle % (Math.PI * 2))) + Math.PI * 2 + Math.PI * 1.5) % (Math.PI * 2)
    return SEGMENTS[Math.floor(normalized / segAngle) % SEGMENTS.length]
}

function showResult() {
    const seg = getWinningSegment()
    if (seg.type === 'no_prize') {
        spinTitle.value = t('spin.thankYou')
        spinSubtitle.value = t('spin.noLuck')
    } else {
        spinTitle.value = t('spin.youWon')
        spinSubtitle.value = `${seg.label} 🎉`
        showConfetti.value = true
    }
    // Tunggu 2 detik biar user lihat hasilnya, baru redirect ke result page
    setTimeout(() => router.visit(resultAction()), 2500)
}

function doSpin() {
    if (spinning.value || props.alreadySpun) return
    spinning.value = true
    btnLabel.value = '...'
    form.submit(spinAction())
}

// Dipanggil setelah redirect balik dari POST /spin
// (backend redirect balik ke /spin dengan alreadySpun = true & prize terisi)
onMounted(() => {
    drawWheel(0)
    if (props.alreadySpun && props.prize) {
        spinning.value = true
        // Hitung target angle supaya pointer tepat di segmen yang menang
        const segAngle = (Math.PI * 2) / SEGMENTS.length
        const targetIdx = SEGMENTS.findIndex(s => s.type === props.prize!.prize_type)
        const targetAngle = (Math.PI * 2 * 5) + (Math.PI * 2 - (targetIdx * segAngle + segAngle / 2)) + Math.PI * 1.5
        animateSpin(targetAngle, 4000, () => {
            spinning.value = false
            showResult()
        })
    }
})

onUnmounted(() => {
    if (rafId) cancelAnimationFrame(rafId)
})
</script>

<template>
    <div
        class="relative min-h-screen flex flex-col items-center justify-center gap-6 px-6 py-12 overflow-hidden"
        style="background: linear-gradient(160deg, #2C1A0E 0%, #3D2410 45%, #1a0f06 100%);"
    >
        <!-- Dekorasi -->
        <div class="pointer-events-none absolute -top-16 -right-16 w-64 h-64 rounded-full"
             style="background: radial-gradient(circle, rgba(212,160,23,0.2) 0%, transparent 70%);" />
        <div class="pointer-events-none absolute bottom-20 -left-10 w-48 h-48 rounded-full"
             style="background: radial-gradient(circle, rgba(212,160,23,0.1) 0%, transparent 70%);" />
        <div class="absolute top-0 left-0 right-0 h-[3px]"
             style="background: linear-gradient(90deg, transparent, #D4A017, #F5C842, #D4A017, transparent);" />

        <!-- Konfeti -->
        <Transition name="confetti">
            <div v-if="showConfetti" class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    v-for="i in 40" :key="i"
                    class="absolute w-2 h-2 rounded-sm animate-bounce"
                    :style="{
                        left: `${10 + Math.random() * 80}%`,
                        top: `${10 + Math.random() * 60}%`,
                        background: ['#D4A017','#F5C842','#C2185B','#1E5FA8','#fff'][i % 5],
                        animationDelay: `${(i * 0.05).toFixed(2)}s`,
                        animationDuration: `${0.6 + (i % 4) * 0.2}s`,
                        transform: `rotate(${i * 37}deg)`,
                    }"
                />
            </div>
        </Transition>

        <!-- Header -->
        <div class="text-center z-10">
            <div class="inline-flex items-center gap-2 rounded-full border px-4 py-1 mb-4"
                 style="background: rgba(212,160,23,0.12); border-color: rgba(212,160,23,0.3);">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D4A017]" />
                <span class="text-[11px] font-semibold tracking-widest text-[#D4A017] uppercase">Bali in Bali</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white transition-all duration-500">{{ spinTitle }}</h1>
            <p class="text-sm mt-1 transition-all duration-500" style="color: rgba(255,255,255,0.5);">{{ spinSubtitle }}</p>
        </div>

        <!-- Roda -->
        <div class="relative z-10" style="width: 280px; height: 280px;">
            <!-- Pointer -->
            <svg class="absolute left-1/2 -top-4 -translate-x-1/2 z-20 drop-shadow-lg"
                 width="28" height="36" viewBox="0 0 28 36">
                <polygon points="14,2 26,34 14,26 2,34" fill="#F5C842" stroke="#B8860B" stroke-width="1"/>
                <polygon points="14,7 21,32 14,25 7,32" fill="#D4A017"/>
            </svg>

            <!-- Ring emas -->
            <div class="absolute inset-0 rounded-full p-[6px]"
                 style="background: linear-gradient(135deg, #F5C842, #8B6914, #F5C842, #D4A017);
                        box-shadow: 0 0 40px rgba(212,160,23,0.4), 0 0 80px rgba(212,160,23,0.15);">
                <canvas
                    ref="canvasRef"
                    width="268"
                    height="268"
                    class="rounded-full"
                    :class="{ 'opacity-90': spinning }"
                />
            </div>

            <!-- Tombol tengah -->
            <button
                type="button"
                :disabled="spinning || alreadySpun"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10
                       w-16 h-16 rounded-full border-4 border-[#2C1A0E]
                       text-xs font-extrabold tracking-wider text-[#2C1A0E]
                       disabled:opacity-60 disabled:cursor-not-allowed
                       transition-transform hover:scale-105 active:scale-95"
                :class="{ 'animate-pulse': spinning }"
                style="background: linear-gradient(135deg, #F5C842, #D4A017);
                       box-shadow: 0 0 20px rgba(212,160,23,0.5);"
                @click="doSpin"
            >
                {{ btnLabel }}
            </button>
        </div>

        <!-- Prize chips -->
        <div class="flex gap-2 flex-wrap justify-center z-10">
            <span class="rounded-full border px-3 py-1 text-xs font-semibold text-[#D4A017]"
                  style="background: rgba(212,160,23,0.12); border-color: rgba(212,160,23,0.3);">
                🎁 Rp 100.000
            </span>
            <span class="rounded-full border px-3 py-1 text-xs"
                  style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                Rp 50.000
            </span>
            <span class="rounded-full border px-3 py-1 text-xs"
                  style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                Rp 25.000
            </span>
        </div>

        <p class="text-[11px] z-10" style="color: rgba(255,255,255,0.3);">{{ t('spin.footerNote') }}</p>
    </div>
</template>