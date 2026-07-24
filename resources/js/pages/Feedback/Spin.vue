<!-- 
<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
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

function showResult() {
    const prize = props.prize
    if (!prize || prize.prize_type === 'no_prize') {
        spinTitle.value = t('spin.thankYou')
        spinSubtitle.value = t('spin.noLuck')
    } else {
        spinTitle.value = t('spin.youWon')
        spinSubtitle.value = `Rp ${prize.voucher_amount?.toLocaleString('id-ID')} 🎉`
        showConfetti.value = true
    }
    setTimeout(() => router.visit(resultAction()), 2500)
}

function doSpin() {
    if (spinning.value || props.alreadySpun) return
    spinning.value = true
    btnLabel.value = '...'

    router.post(spinAction().url, {}, {
        preserveState: false,
        preserveScroll: false,
        onError: () => {
            spinning.value = false
            btnLabel.value = t('spin.button')
        },
    })
}

onMounted(() => {
    drawWheel(0)
    if (props.alreadySpun && props.prize) {
        spinning.value = true
        const segAngle = (Math.PI * 2) / SEGMENTS.length
        const matchingIndices = SEGMENTS
            .map((s, i) => s.type === props.prize!.prize_type ? i : -1)
            .filter(i => i !== -1)
        const targetIdx = matchingIndices[Math.floor(Math.random() * matchingIndices.length)]
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
        class="relative flex min-h-screen flex-col items-center justify-center gap-6 overflow-hidden px-6 py-12"
        style="background: linear-gradient(160deg, #2C1A0E 0%, #3D2410 45%, #1a0f06 100%);"
    >
        <div class="pointer-events-none absolute -top-16 -right-16 h-64 w-64 rounded-full"
             style="background: radial-gradient(circle, rgba(212,160,23,0.2) 0%, transparent 70%);" />
        <div class="pointer-events-none absolute bottom-20 -left-10 h-48 w-48 rounded-full"
             style="background: radial-gradient(circle, rgba(212,160,23,0.1) 0%, transparent 70%);" />
        <div class="absolute top-0 left-0 right-0 h-[3px]"
             style="background: linear-gradient(90deg, transparent, #D4A017, #F5C842, #D4A017, transparent);" />

        <Transition name="confetti">
            <div v-if="showConfetti" class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    v-for="i in 40" :key="i"
                    class="absolute h-2 w-2 animate-bounce rounded-sm"
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

        <div class="z-10 text-center">
          
            <h1 class="text-2xl font-extrabold text-white transition-all duration-500">{{ spinTitle }}</h1>
            <p class="mt-1 text-sm transition-all duration-500" style="color: rgba(255,255,255,0.5);">{{ spinSubtitle }}</p>
        </div>

        <div class="relative z-10" style="width: 280px; height: 280px;">
            <svg class="absolute left-1/2 -top-4 z-20 -translate-x-1/2 drop-shadow-lg"
                 width="28" height="36" viewBox="0 0 28 36">
                <polygon points="14,2 26,34 14,26 2,34" fill="#F5C842" stroke="#B8860B" stroke-width="1"/>
                <polygon points="14,7 21,32 14,25 7,32" fill="#D4A017"/>
            </svg>

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

            <button
                type="button"
                :disabled="spinning || alreadySpun"
                class="absolute left-1/2 top-1/2 z-10 h-16 w-16
                       -translate-x-1/2 -translate-y-1/2 rounded-full
                       border-4 border-[#2C1A0E] text-xs font-extrabold
                       tracking-wider text-[#2C1A0E] transition-transform
                       hover:scale-105 active:scale-95
                       disabled:cursor-not-allowed disabled:opacity-60"
                :class="{ 'animate-pulse': spinning }"
                style="background: linear-gradient(135deg, #F5C842, #D4A017);
                       box-shadow: 0 0 20px rgba(212,160,23,0.5);"
                @click="doSpin"
            >
                {{ btnLabel }}
            </button>
        </div>

        <div class="z-10 flex flex-wrap justify-center gap-2">
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

        <p class="z-10 text-[11px]" style="color: rgba(255,255,255,0.3);">{{ t('spin.footerNote') }}</p>
    </div>
</template> -->
<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
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
const canvasRef = ref<HTMLCanvasElement | null>(null)
const spinning = ref(false)
const btnLabel = ref(t('spin.button'))
const spinTitle = ref(t('spin.title'))
const spinSubtitle = ref(t('spin.subtitle'))
const showConfetti = ref(false)

const SEGMENTS = [
    { type: 'voucher_50k',  label: 'Rp 50.000',  color: '#C2185B', textColor: '#fff' },
    { type: 'voucher_100k', label: 'Rp 100.000', color: '#4A4A6A', textColor: '#fff' },
    { type: 'no_prize',     label: t('spin.thankYou'), color: '#2B2B2B', textColor: '#888' },
    { type: 'voucher_25k',  label: 'Rp 25.000',  color: '#1E5FA8', textColor: '#fff' },
    { type: 'voucher_50k',  label: 'Rp 50.000',  color: '#C2185B', textColor: '#fff' },
    { type: 'voucher_100k', label: 'Rp 100.000', color: '#4A4A6A', textColor: '#fff' },
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
        ctx.strokeStyle = 'rgba(255,255,255,0.08)'
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

    // Center circle
    ctx.beginPath()
    ctx.arc(cx, cy, 32, 0, Math.PI * 2)
    ctx.fillStyle = '#18181b'
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

function showResult() {
    const prize = props.prize
    if (!prize || prize.prize_type === 'no_prize') {
        spinTitle.value = t('spin.thankYou')
        spinSubtitle.value = t('spin.noLuck')
    } else {
        spinTitle.value = t('spin.youWon')
        spinSubtitle.value = `Rp ${prize.voucher_amount?.toLocaleString('id-ID')} 🎉`
        showConfetti.value = true
    }
    setTimeout(() => router.visit(resultAction()), 2500)
}

function doSpin() {
    if (spinning.value || props.alreadySpun) return
    spinning.value = true
    btnLabel.value = '...'

    router.post(spinAction().url, {}, {
        preserveState: false,
        preserveScroll: false,
        onError: () => {
            spinning.value = false
            btnLabel.value = t('spin.button')
        },
    })
}

onMounted(() => {
    drawWheel(0)
    if (props.alreadySpun && props.prize) {
        spinning.value = true
        const segAngle = (Math.PI * 2) / SEGMENTS.length
        const matchingIndices = SEGMENTS
            .map((s, i) => s.type === props.prize!.prize_type ? i : -1)
            .filter(i => i !== -1)
        const targetIdx = matchingIndices[Math.floor(Math.random() * matchingIndices.length)]
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
        class="relative flex min-h-screen flex-col items-center justify-center gap-6 overflow-hidden px-6 py-12"
        style="background: #09090b;"
    >
        <!-- Subtle top border -->
        <div class="absolute top-0 left-0 right-0 h-[2px]"
             style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);" />

        <!-- Confetti -->
        <Transition name="confetti">
            <div v-if="showConfetti" class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    v-for="i in 40" :key="i"
                    class="absolute h-2 w-2 animate-bounce rounded-sm"
                    :style="{
                        left: `${10 + Math.random() * 80}%`,
                        top: `${10 + Math.random() * 60}%`,
                        background: ['#C2185B','#1E5FA8','#fff','#6366f1','#e2e8f0'][i % 5],
                        animationDelay: `${(i * 0.05).toFixed(2)}s`,
                        animationDuration: `${0.6 + (i % 4) * 0.2}s`,
                        transform: `rotate(${i * 37}deg)`,
                    }"
                />
            </div>
        </Transition>

        <!-- Title -->
        <div class="z-10 text-center">
            <h1 class="text-2xl font-extrabold text-white transition-all duration-500">{{ spinTitle }}</h1>
            <p class="mt-1 text-sm transition-all duration-500" style="color: rgba(255,255,255,0.4);">{{ spinSubtitle }}</p>
        </div>

        <!-- Wheel -->
        <div class="relative z-10" style="width: 280px; height: 280px;">
            <!-- Pointer arrow -->
            <svg class="absolute left-1/2 -top-4 z-20 -translate-x-1/2 drop-shadow"
                 width="28" height="36" viewBox="0 0 28 36">
                <polygon points="14,2 26,34 14,26 2,34" fill="#e2e8f0" stroke="#64748b" stroke-width="1"/>
                <polygon points="14,7 21,32 14,25 7,32" fill="#94a3b8"/>
            </svg>

            <!-- Wheel ring -->
            <div class="absolute inset-0 rounded-full p-[6px]"
                 style="background: #27272a;
                        box-shadow: 0 0 0 2px rgba(255,255,255,0.06), 0 8px 32px rgba(0,0,0,0.6);">
                <canvas
                    ref="canvasRef"
                    width="268"
                    height="268"
                    class="rounded-full"
                    :class="{ 'opacity-90': spinning }"
                />
            </div>

            <!-- Center spin button -->
            <button
                type="button"
                :disabled="spinning || alreadySpun"
                class="absolute left-1/2 top-1/2 z-10 h-16 w-16
                       -translate-x-1/2 -translate-y-1/2 rounded-full
                       border-2 border-zinc-700 text-xs font-extrabold
                       tracking-wider text-white transition-transform
                       hover:scale-105 active:scale-95
                       disabled:cursor-not-allowed disabled:opacity-50"
                :class="{ 'animate-pulse': spinning }"
                style="background: #27272a;
                       box-shadow: 0 4px 16px rgba(0,0,0,0.5);"
                @click="doSpin"
            >
                {{ btnLabel }}
            </button>
        </div>

        <!-- Prize badges -->
        <div class="z-10 flex flex-wrap justify-center gap-2">
            <span class="rounded-full border px-3 py-1 text-xs"
                  style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.5);">
                 Rp 100.000
            </span>
            <span class="rounded-full border px-3 py-1 text-xs"
                  style="background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5);">
                Rp 50.000
            </span>
            <span class="rounded-full border px-3 py-1 text-xs"
                  style="background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5);">
                Rp 25.000
            </span>
        </div>

        <p class="z-10 text-[11px]" style="color: rgba(255,255,255,0.25);">{{ t('spin.footerNote') }}</p>
    </div>
</template>