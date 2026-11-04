<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kalkulator HPP — {{ $shop->name ?? 'OQARI' }}</title>

    
    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .sparta-hpp { font-family: 'DM Sans', sans-serif; font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11'; }
        
        /* Modern form inputs & select styles */
        .hpp-input {
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #dcdfe4;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13.5px;
            font-weight: 500;
            color: #111827;
            height: 46px;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }
        .hpp-input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: #df5826;
            box-shadow: 0 0 0 3px rgba(223, 88, 38, 0.12);
        }
        .hpp-input:disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }
        .hpp-input-prefix-icon {
            padding-left: 42px !important;
        }
        .hpp-input-prefix-rp {
            padding-left: 46px !important;
        }
        .hpp-input-suffix {
            padding-right: 38px !important;
        }

        /* Hide number spin buttons */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="sparta-hpp min-h-screen bg-[#f6f5f2] text-[#111827] antialiased" x-data="hppCalculator()">
<div class="cafe-page-shell">
    <x-cafe-sidebar />
    
    <div class="cafe-page-content flex flex-col min-h-screen bg-[#f6f5f2]">
        
        {{-- TOP HEADER BAR (Sparta Caffe Style) --}}
        <header class="h-[52px] bg-[#f6f5f2] border-b border-[#e6e5e0] px-4 lg:px-[26px] flex items-center justify-between shrink-0 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button type="button" @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
                    class="w-9 h-9 rounded-lg bg-[#eae8e3] flex items-center justify-center text-[#111827] hover:bg-[#dfddd7] transition-colors md:hidden">
                    <i class="fas fa-bars text-base"></i>
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-[13px] font-semibold text-[#111827]">Kalkulator HPP &amp; Resep</span>
                    @if($product)
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#fff6f0] text-[#df5826] border border-[#df5826]/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#df5826]"></span>
                            {{ $product->name }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-1.5 bg-[#f1f0ec] border border-[#e6e5e0] h-[34px] px-[10px] rounded-[9px] text-[11px] font-medium text-[#53565a]">
                    <img src="{{ asset('images/sparta/clock.svg') }}" alt="" class="w-3.5 h-3.5 opacity-75">
                    <span>{{ now()->setTimezone($shop?->normalized_operating_hours['timezone'] ?? 'Asia/Jakarta')->format('H:i') }}</span>
                </div>
                <div class="bg-[#f1f0ec] border border-[#e6e5e0] h-[34px] px-[10px] rounded-[9px] flex items-center text-[11px] font-medium text-[#53565a]">
                    {{ now()->translatedFormat('d M Y') }}
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main id="hpp-main" class="flex-1 w-full max-w-[1400px] mx-auto p-4 md:p-[24px] space-y-6 pb-28">

            {{-- 1. TITLE & PRODUCT SELECTOR SECTION --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold text-[#df5826] uppercase tracking-[0.22px]">Resep · Biaya Produksi · Margin Keuntungan</p>
                    <h1 class="text-[22px] md:text-[26px] font-bold text-[#111827] tracking-tight mt-0.5">Kalkulator HPP &amp; Simulasi Harga</h1>
                    <p class="text-[12px] md:text-[13px] text-[#7f837e] mt-0.5">
                        Susun takaran resep, hitung biaya bahan baku, tenaga kerja, dan overhead untuk satu siklus produksi kafe.
                    </p>
                </div>

                {{-- Quick Product Switcher --}}
                <div class="bg-white border border-[#e6e5e0] rounded-2xl p-2.5 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <form method="GET" action="{{ route('admin.hpp.index') }}" class="flex items-center gap-2 grow">
                        <div class="relative grow min-w-[240px]">
                            <select id="product-id" name="product_id" class="w-full bg-[#f8f9fa] border border-[#e6e5e0] rounded-xl px-3 py-2 text-xs font-semibold text-[#111827] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826]/20 transition-all cursor-pointer">
                                <option value="">— Simulasi Bebas (Tanpa Terhubung Produk) —</option>
                                @foreach($products as $choice)
                                    <option value="{{ $choice->id }}" @selected($product?->id === $choice->id)>
                                        {{ $choice->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-gradient-to-r from-[#f06428] to-[#df5826] text-white font-bold text-xs shadow-sm hover:opacity-95 transition-opacity flex items-center gap-1.5">
                            <i class="fas fa-folder-open text-xs"></i>
                            <span>Muat</span>
                        </button>
                    </form>
                    @if($product)
                        <a href="{{ route('admin.hpp.index') }}" class="shrink-0 px-3 py-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 text-xs font-semibold transition-colors text-center" title="Beralih ke simulasi mandiri">
                            Reset ke Bebas
                        </a>
                    @endif
                </div>
            </div>

            {{-- Product Status Pill Alert (If loaded) --}}
            @if($product)
                <div class="bg-white border border-[#e6e5e0] rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-white via-white to-orange-50/40">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#fff6f0] border border-[#df5826]/20 text-[#df5826] flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-burger"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-sm text-[#111827]">{{ $product->name }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                    ● Tersambung Menu
                                </span>
                            </div>
                            <p class="text-xs text-[#7f837e] mt-0.5">
                                Harga Jual Kasir: <strong class="text-[#111827]">Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
                                · HPP Tersimpan: <strong class="text-[#111827]">Rp {{ number_format($product->cogs, 0, ',', '.') }}</strong>
                                · Laba Kotor: <strong class="text-[#289e5c]">Rp {{ number_format(max(0, $product->price - $product->cogs), 0, ',', '.') }} ({{ $product->price > 0 ? round((($product->price - $product->cogs) / $product->price) * 100, 1) : 0 }}%)</strong>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400">Tekan "Simpan HPP" pada langkah akhir untuk memperbarui resep produk ini</span>
                    </div>
                </div>
            @endif

            {{-- 2. MODERN STEPPER PROGRESS BAR (Sparta Aesthetic) --}}
            <nav aria-label="Tahap perhitungan HPP" class="bg-white border border-[#e6e5e0] rounded-2xl p-2.5 md:p-3 shadow-sm overflow-x-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 min-w-[650px] lg:min-w-0">
                    <template x-for="(title, index) in steps" :key="title">
                        <button type="button" 
                                @click="goTo(index + 1)" 
                                :disabled="busy || index + 1 > furthest" 
                                :aria-current="step === index + 1 ? 'step' : null"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition-all text-left group"
                                :class="{
                                    'bg-[#fff6f0] border-[#df5826] shadow-sm': step === index + 1,
                                    'bg-white border-[#e6e5e0] hover:border-gray-300': step !== index + 1 && index + 1 <= furthest,
                                    'bg-gray-50/60 border-transparent opacity-50 cursor-not-allowed': index + 1 > furthest
                                }">
                            
                            {{-- Step Number / Check Circle --}}
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 transition-colors"
                                 :class="{
                                     'bg-[#df5826] text-white shadow-sm ring-2 ring-[#df5826]/20': step === index + 1,
                                     'bg-[#289e5c] text-white': step > index + 1,
                                     'bg-gray-100 text-gray-600': step < index + 1 && index + 1 <= furthest,
                                     'bg-gray-100 text-gray-400': index + 1 > furthest
                                 }">
                                <template x-if="step > index + 1">
                                    <i class="fas fa-check text-[10px]"></i>
                                </template>
                                <template x-if="step <= index + 1">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>

                            {{-- Step Label --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] uppercase font-bold tracking-wider leading-tight"
                                   :class="step === index + 1 ? 'text-[#df5826]' : (step > index + 1 ? 'text-[#289e5c]' : 'text-gray-400')"
                                   x-text="'Langkah ' + (index + 1)"></p>
                                <p class="text-xs font-bold truncate leading-tight mt-0.5"
                                   :class="step === index + 1 ? 'text-[#111827]' : (step > index + 1 ? 'text-[#111827]' : 'text-gray-500')"
                                   x-text="title"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </nav>

            {{-- Status / Error Alerts --}}
            <div id="hpp-error-banner" x-cloak x-show="error" role="alert" tabindex="-1" x-ref="errorBox" class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-2xl flex items-start gap-3 shadow-sm" x-transition>
                <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-triangle-exclamation text-sm"></i>
                </div>
                <div class="flex-1 pt-1">
                    <h5 class="text-xs font-bold text-red-900 mb-0.5">Perhatian</h5>
                    <p class="text-xs font-medium text-red-800" x-text="error"></p>
                </div>
                <button type="button" @click="error = ''" class="text-red-400 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-100 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            <div x-cloak x-show="saved" role="status" class="p-4 bg-green-50 text-green-900 border border-green-200 rounded-2xl flex items-start gap-3 shadow-sm" x-transition>
                <i class="fas fa-circle-check text-green-600 mt-0.5 text-base"></i>
                <div class="text-xs md:text-sm font-semibold flex-1" x-text="saved"></div>
                <button type="button" @click="saved = ''" class="text-green-400 hover:text-green-600 p-1"><i class="fas fa-times"></i></button>
            </div>

            {{-- 3. MAIN WORKFLOW: 2-COLUMN LAYOUT (FORM + REALTIME SIDEBAR SUMMARY) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- LEFT COLUMN: WIZARD FORM (8 Cols) --}}
                <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-6">
                    <form @submit.prevent="next()" @input="invalidate()" novalidate>

                        {{-- STEP 1: TARGET PRODUKSI --}}
                        <div id="hpp-step-1" x-show="step === 1" class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.02)] space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#df5826] flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div>
                                    <h2 class="text-base md:text-lg font-bold text-[#111827]">Target Output Produksi</h2>
                                    <p class="text-xs text-[#7f837e]">Tentukan berapa banyak produk yang dihasilkan dalam 1 siklus pembuatan/peracikan.</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-[#f8f9fa] border border-[#e6e5e0] text-xs text-[#53565a] flex items-start gap-3">
                                <i class="fas fa-info-circle text-[#df5826] text-base shrink-0 mt-0.5"></i>
                                <div class="leading-relaxed">
                                    <strong>Tips Siklus Produksi:</strong> Semua bahan baku, tenaga kerja, dan overhead pada langkah berikutnya dihitung untuk <strong>satu siklus penuh</strong> ini. 
                                    Satu unit output harus sama dengan satuan produk yang dijual ke konsumen (misalnya: 1 cup, 10 porsi, atau 5 botol).
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="production-quantity" class="block text-xs font-bold text-[#111827] mb-2">
                                        Jumlah Hasil Produksi per Siklus <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                            <i class="fas fa-hashtag text-xs"></i>
                                        </div>
                                        <input id="production-quantity" x-model="form.production_quantity" type="number" min="0.01" max="999999" step="0.01" required 
                                            class="hpp-input hpp-input-prefix-icon" placeholder="Contoh: 1 atau 10">
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Berapa porsi/cup yang dihasilkan dalam 1 batch.</p>
                                </div>

                                <div>
                                    <label for="production-unit" class="block text-xs font-bold text-[#111827] mb-2">
                                        Satuan Output <span class="text-red-500">*</span>
                                    </label>
                                    <select id="production-unit" x-model="form.production_unit" class="hpp-input cursor-pointer">
                                        <template x-for="unit in outputUnits" :key="unit">
                                            <option :value="unit" x-text="unit"></option>
                                        </template>
                                    </select>
                                    <p class="text-[11px] text-gray-400 mt-1">Satuan jual (contoh: porsi, pcs, cup, botol).</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="selling-price" class="block text-xs font-bold text-[#111827] mb-2">
                                        Simulasi Harga Jual per Unit (Rp) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <span class="text-xs font-black text-gray-400">Rp</span>
                                        </div>
                                        <input id="selling-price" x-model="form.selling_price" type="number" min="0" max="99999999.99" step="0.01" required 
                                            class="hpp-input hpp-input-prefix-rp text-base font-bold text-gray-900" placeholder="Contoh: 25000">
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Harga jual yang Anda targetkan untuk melihat estimasi laba kotor &amp; margin.</p>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 2: BAHAN BAKU & KEMASAN --}}
                        <div id="hpp-step-2" x-show="step === 2" class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.02)] space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#df5826] flex items-center justify-center text-lg shrink-0">
                                        <i class="fas fa-boxes-stacked"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-base md:text-lg font-bold text-[#111827]">Bahan Baku &amp; Kemasan</h2>
                                        <p class="text-xs text-[#7f837e]">Masukkan semua takaran bahan yang dibutuhkan untuk 1 siklus produksi.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @can('manage-inventory')
                                        <button type="button" @click="showNewMaterialModal = true" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-[#111827] font-bold text-xs transition-colors flex items-center gap-1.5">
                                            <i class="fas fa-plus text-[10px]"></i>
                                            <span>Bahan Baru</span>
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            {{-- Material Quick Filter --}}
                            <div class="relative flex items-center">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-search text-xs"></i>
                                </div>
                                <input id="material-search" type="search" x-model="materialSearch" 
                                    class="hpp-input hpp-input-prefix-icon" placeholder="Cari nama bahan baku yang tersedia di inventori…">
                            </div>

                            <div x-show="materials.length === 0" class="p-4 rounded-xl bg-amber-50 text-amber-900 border border-amber-200 text-xs">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Belum ada data bahan baku di toko. Klik <strong>Bahan Baru</strong> untuk menambahkan bahan langsung.
                            </div>

                            {{-- Recipe Rows List --}}
                            <div class="space-y-4">
                                <template x-for="(row, index) in form.recipes" :key="row.key">
                                    <div class="border border-[#e6e5e0] rounded-2xl p-4 bg-[#fcfcfd] hover:bg-white transition-all shadow-[0_2px_8px_rgba(0,0,0,0.01)] relative">
                                        
                                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-md bg-gray-200/70 text-[#111827] flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                                                <span class="text-xs font-bold text-[#111827]">Komposisi Resep</span>
                                            </div>
                                            
                                            {{-- Estimated cost for this row --}}
                                            <div class="flex items-center gap-3">
                                                <div class="text-right">
                                                    <span class="text-[10px] text-gray-400 block leading-tight">Estimasi Biaya</span>
                                                    <span class="text-xs font-bold text-[#df5826]" x-text="unitMoney(rowEstimatedCost(row))"></span>
                                                </div>
                                                <button type="button" @click="removeRow(index)" :disabled="form.recipes.length <= 1" class="w-7 h-7 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Hapus bahan ini">
                                                    <i class="far fa-trash-alt text-xs"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid sm:grid-cols-12 gap-3 items-start">
                                            {{-- Select Material --}}
                                            <div class="sm:col-span-6">
                                                <label :for="'material-' + row.key" class="block text-[11px] font-bold text-gray-700 mb-1">Pilih Bahan Baku</label>
                                                <select :id="'material-' + row.key" x-model="row.raw_material_id" @change="row.unit = ''; invalidate()" class="hpp-input cursor-pointer" required>
                                                    <option value="">-- Pilih Bahan Baku dari Stok --</option>
                                                    <template x-for="material in filteredMaterials(row)" :key="material.id">
                                                        <option :value="material.id" x-text="material.name + ' (' + unitMoney(material.cost_per_unit) + ' / ' + material.unit + ')'"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            {{-- Takaran Amount --}}
                                            <div class="sm:col-span-3">
                                                <label :for="'amount-' + row.key" class="block text-[11px] font-bold text-gray-700 mb-1">Takaran 1 Siklus</label>
                                                <input :id="'amount-' + row.key" x-model="row.amount" type="number" step="0.000001" min="0.000001" max="999999.99" required class="hpp-input" placeholder="0">
                                            </div>

                                            {{-- Satuan Unit --}}
                                            <div class="sm:col-span-3">
                                                <label :for="'unit-' + row.key" class="block text-[11px] font-bold text-gray-700 mb-1">Satuan Takaran</label>
                                                <select :id="'unit-' + row.key" x-model="row.unit" @change="invalidate()" class="hpp-input cursor-pointer">
                                                    <option value="">Satuan Stok</option>
                                                    <template x-for="unit in recipeUnits(row)" :key="unit">
                                                        <option :value="unit" x-text="unit"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            {{-- Notes / Petunjuk Khusus --}}
                                            <div class="sm:col-span-12">
                                                <input :id="'notes-' + row.key" x-model="row.notes" maxlength="500" class="hpp-input py-1.5 text-xs placeholder:text-gray-400" placeholder="Catatan resep (opsional, contoh: gunakan susu fresh dingin atau cup 16oz)">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Add Row Button --}}
                            <div>
                                <button type="button" @click="addRow()" :disabled="form.recipes.length >= 100" class="w-full py-3 rounded-xl border-2 border-dashed border-[#df5826]/40 hover:border-[#df5826] text-[#df5826] bg-[#fff6f0]/50 hover:bg-[#fff6f0] font-bold text-xs transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-plus text-xs"></i>
                                    <span>Tambah Bahan Baku / Kemasan Lain</span>
                                </button>
                            </div>
                        </div>

                        {{-- STEP 3: BIAYA TENAGA KERJA --}}
                        <div id="hpp-step-3" x-show="step === 3" class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.02)] space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#df5826] flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <div>
                                    <h2 class="text-base md:text-lg font-bold text-[#111827]">Biaya Tenaga Kerja Langsung (Labor Cost)</h2>
                                    <p class="text-xs text-[#7f837e]">Alokasikan upah langsung kru/barista/koki untuk menyelesaikan satu siklus produksi ini.</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-[#f8f9fa] border border-[#e6e5e0] text-xs text-[#53565a] flex items-start gap-3">
                                <i class="fas fa-lightbulb text-amber-500 text-base shrink-0 mt-0.5"></i>
                                <div class="leading-relaxed">
                                    <strong>Cara Perhitungan:</strong> Jika 1 barista digaji Rp 20.000/jam dan proses batch butuh 15 menit (0.25 jam), maka biaya tenaga kerja = <strong>Rp 5.000</strong>. Jika upah karyawan sudah dialokasikan ke gaji bulanan operasional, Anda dapat mengisi <strong>0</strong>.
                                </div>
                            </div>

                            <div>
                                <label for="labor-cost" class="block text-xs font-bold text-[#111827] mb-2">
                                    Total Biaya Tenaga Kerja untuk 1 Siklus (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <span class="text-xs font-black text-gray-400">Rp</span>
                                    </div>
                                    <input id="labor-cost" x-model="form.labor_cost" type="number" min="0" max="99999999.99" step="0.01" required 
                                        class="hpp-input hpp-input-prefix-rp text-base font-bold text-gray-900" placeholder="0">
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Isi 0 jika tidak mengalokasikan upah per siklus.</p>
                            </div>
                        </div>

                        {{-- STEP 4: BIAYA OVERHEAD --}}
                        <div id="hpp-step-4" x-show="step === 4" class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.02)] space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#df5826] flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div>
                                    <h2 class="text-base md:text-lg font-bold text-[#111827]">Biaya Overhead Produksi</h2>
                                    <p class="text-xs text-[#7f837e]">Beban biaya pendukung seperti listrik mesin espresso, gas LPG, es batu, sedotan, dan tissue.</p>
                                </div>
                            </div>

                            <div>
                                <label for="overhead-mode" class="block text-xs font-bold text-[#111827] mb-2">Metode Perhitungan Overhead</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all"
                                           :class="form.overhead_mode === 'fixed' ? 'border-[#df5826] bg-[#fff6f0] text-[#111827]' : 'border-[#e6e5e0] text-gray-600 bg-white'">
                                        <input type="radio" value="fixed" x-model="form.overhead_mode" @change="invalidate()" class="text-[#df5826] focus:ring-[#df5826]">
                                        <div>
                                            <p class="text-xs font-bold">Nominal Tetap (Rp)</p>
                                            <p class="text-[11px] text-gray-500">Jumlah rupiah tertentu per siklus</p>
                                        </div>
                                    </label>

                                    <label class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all"
                                           :class="form.overhead_mode === 'percentage' ? 'border-[#df5826] bg-[#fff6f0] text-[#111827]' : 'border-[#e6e5e0] text-gray-600 bg-white'">
                                        <input type="radio" value="percentage" x-model="form.overhead_mode" @change="invalidate()" class="text-[#df5826] focus:ring-[#df5826]">
                                        <div>
                                            <p class="text-xs font-bold">Persentase (%)</p>
                                            <p class="text-[11px] text-gray-500">% dari (Bahan Baku + Tenaga Kerja)</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Fixed Mode --}}
                            <div x-show="form.overhead_mode === 'fixed'">
                                <label for="overhead-cost" class="block text-xs font-bold text-[#111827] mb-2">
                                    Biaya Overhead per Siklus (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <span class="text-xs font-black text-gray-400">Rp</span>
                                    </div>
                                    <input id="overhead-cost" x-model="form.overhead_cost" type="number" min="0" max="99999999.99" step="0.01" :required="form.overhead_mode === 'fixed'" 
                                        class="hpp-input hpp-input-prefix-rp text-base font-bold text-gray-900" placeholder="Contoh: 5000">
                                </div>
                            </div>

                            {{-- Percentage Mode --}}
                            <div x-show="form.overhead_mode === 'percentage'" class="space-y-3">
                                <label for="overhead-percent" class="block text-xs font-bold text-[#111827]">
                                    Persentase Overhead (0–100%) <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-4">
                                    <input id="overhead-slider" x-model="form.overhead_percent" type="range" min="0" max="100" step="0.5" class="w-full accent-[#df5826]">
                                    <div class="relative w-28 shrink-0 flex items-center">
                                        <input id="overhead-percent" x-model="form.overhead_percent" type="number" min="0" max="100" step="0.01" :required="form.overhead_mode === 'percentage'" 
                                            class="hpp-input hpp-input-suffix text-right font-bold" placeholder="10">
                                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                            <span class="text-xs font-bold text-gray-400">%</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-400">Umumnya kafe menetapkan 5% – 15% untuk biaya penunjang.</p>
                            </div>
                        </div>

                        {{-- STEP 5: HASIL, MARGIN & REKOMENDASI HARGA --}}
                        <div id="hpp-step-5" x-show="step === 5" class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.02)] space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#df5826] flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <h2 class="text-base md:text-lg font-bold text-[#111827]">Analisis Margin &amp; Rekomendasi Harga Jual</h2>
                                    <p class="text-xs text-[#7f837e]">Tentukan target profit margin atau markup untuk menghasilkan rekomendasi harga jual optimal.</p>
                                </div>
                            </div>

                            {{-- Pricing Target Method --}}
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="pricing-method" class="block text-xs font-bold text-[#111827] mb-2">Metode Rekomendasi Harga</label>
                                    <select id="pricing-method" x-model="form.pricing_method" @change="invalidate()" class="hpp-input cursor-pointer font-semibold">
                                        <option value="margin">Gross Profit Margin (% dari Harga Jual)</option>
                                        <option value="markup">Markup (% di Atas Biaya HPP)</option>
                                    </select>
                                </div>

                                <div x-show="form.pricing_method === 'margin'">
                                    <label for="target-margin" class="block text-xs font-bold text-[#111827] mb-2">Target Margin (%) — Opsional</label>
                                    <div class="relative flex items-center">
                                        <input id="target-margin" x-model="form.target_margin" type="number" min="0" max="99.99" step="0.01" 
                                            class="hpp-input hpp-input-suffix font-bold" placeholder="Contoh: 60">
                                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                            <span class="text-xs font-bold text-gray-400">%</span>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Rumus: HPP ÷ (1 − Target Margin)</p>
                                </div>

                                <div x-show="form.pricing_method === 'markup'">
                                    <label for="target-markup" class="block text-xs font-bold text-[#111827] mb-2">Target Markup (%) — Opsional</label>
                                    <div class="relative flex items-center">
                                        <input id="target-markup" x-model="form.target_markup" type="number" min="0" max="1000" step="0.01" 
                                            class="hpp-input hpp-input-suffix font-bold" placeholder="Contoh: 150">
                                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                            <span class="text-xs font-bold text-gray-400">%</span>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Rumus: HPP × (1 + Target Markup)</p>
                                </div>
                            </div>

                            <button type="button" @click="calculate()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#f06428] to-[#df5826] text-white font-bold text-xs shadow-sm hover:opacity-95 transition-all flex items-center gap-2">
                                <i class="fas fa-arrows-rotate text-xs"></i>
                                <span>Hitung Ulang &amp; Segarkan Angka</span>
                            </button>

                            {{-- Detailed Breakdown Tables --}}
                            <template x-if="result">
                                <div class="space-y-4 pt-2">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-xs">
                                            <thead>
                                                <tr class="bg-[#f8f9fa] text-[#7f837e] uppercase font-bold text-[11px]">
                                                    <th class="py-3 px-3.5 rounded-l-xl">Bahan / Kemasan</th>
                                                    <th class="py-3 px-3.5 text-center">Takaran 1 Siklus</th>
                                                    <th class="py-3 px-3.5 text-center">Takaran per Unit</th>
                                                    <th class="py-3 px-3.5 text-right rounded-r-xl">Biaya (1 Siklus)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 font-medium">
                                                <template x-for="line in result.lines" :key="line.raw_material_id">
                                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                                        <td class="py-3 px-3.5">
                                                            <span class="font-bold text-[#111827]" x-text="line.name"></span>
                                                            <small class="block text-gray-400 text-[10px]" x-show="line.notes" x-text="line.notes"></small>
                                                        </td>
                                                        <td class="py-3 px-3.5 text-center text-gray-600" x-text="line.amount + ' ' + line.unit"></td>
                                                        <td class="py-3 px-3.5 text-center text-gray-600" x-text="(Number(line.amount) / Number(form.production_quantity)).toFixed(4) + ' ' + line.unit"></td>
                                                        <td class="py-3 px-3.5 text-right font-bold text-[#111827]" x-text="money(line.cost)"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- STEP 6: SIMULASI PAJAK & HARGA FINAL --}}
                        <div id="hpp-step-6" x-show="step === 6" class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.02)] space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#df5826] flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div>
                                    <h2 class="text-base md:text-lg font-bold text-[#111827]">Pajak Penjualan &amp; Harga Konsumen Final</h2>
                                    <p class="text-xs text-[#7f837e]">Simulasikan penerapan pajak PB1 / PPN restoran terhadap laba bersih yang Anda terima.</p>
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="tax-mode" class="block text-xs font-bold text-[#111827] mb-2">Perlakuan Pajak</label>
                                    <select id="tax-mode" x-model="form.tax_mode" @change="invalidate()" class="hpp-input cursor-pointer font-semibold">
                                        <option value="exclusive">Ditambahkan ke harga (Exclusive / + Pajak)</option>
                                        <option value="inclusive">Sudah termasuk dalam harga (Inclusive / Netto)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="tax-rate" class="block text-xs font-bold text-[#111827] mb-2">Tarif Pajak (%) — Isi 0 jika Tanpa Pajak</label>
                                    <div class="relative flex items-center">
                                        <input id="tax-rate" x-model="form.tax_rate" type="number" min="0" max="100" step="0.01" required 
                                            class="hpp-input hpp-input-suffix font-bold" placeholder="0">
                                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                            <span class="text-xs font-bold text-gray-400">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" @click="calculate()" class="px-5 py-2.5 rounded-xl border border-[#df5826] text-[#df5826] hover:bg-orange-50 font-bold text-xs transition-colors flex items-center gap-2">
                                <i class="fas fa-arrows-rotate text-xs"></i>
                                <span>Perbarui Perhitungan Pajak</span>
                            </button>

                            {{-- Final Price Detail Card --}}
                            <template x-if="result && result.is_complete">
                                <div class="rounded-2xl border border-gray-200 p-5 bg-[#fcfcfd] space-y-4">
                                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                        <div>
                                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Harga Jual Akhir ke Pelanggan</span>
                                            <h3 class="text-2xl font-bold text-[#111827] mt-0.5" x-text="money(result.final_price) + ' / ' + form.production_unit"></h3>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                            Lengkap &amp; Siap Pakai
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                        <div class="p-3 bg-white rounded-xl border border-gray-100">
                                            <span class="text-gray-400 block text-[11px]">Harga Sebelum Pajak</span>
                                            <span class="font-bold text-[#111827] mt-1 block" x-text="money(result.price_before_tax)"></span>
                                        </div>
                                        <div class="p-3 bg-white rounded-xl border border-gray-100">
                                            <span class="text-gray-400 block text-[11px]">Nominal Pajak / Unit</span>
                                            <span class="font-bold text-gray-600 mt-1 block" x-text="money(result.tax_amount)"></span>
                                        </div>
                                        <div class="p-3 bg-white rounded-xl border border-gray-100">
                                            <span class="text-gray-400 block text-[11px]">Laba Bersih / Unit</span>
                                            <span class="font-bold text-[#289e5c] mt-1 block" x-text="money(result.profit_after_tax)"></span>
                                        </div>
                                        <div class="p-3 bg-white rounded-xl border border-gray-100">
                                            <span class="text-gray-400 block text-[11px]">Margin Bersih</span>
                                            <span class="font-bold text-[#289e5c] mt-1 block" x-text="percent(result.margin_after_tax)"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Product Save Action Box --}}
                            @if($product)
                                <div class="p-5 rounded-2xl bg-[#fff6f0] border border-[#df5826]/30 space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#df5826] text-white flex items-center justify-center shrink-0 mt-0.5">
                                            <i class="fas fa-floppy-disk text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-[#111827]">Simpan HPP &amp; Resep ke Menu {{ $product->name }}</h4>
                                            <p class="text-xs text-[#53565a] mt-0.5 leading-relaxed">
                                                Menyimpan HPP per unit (Rp <span x-text="result?.hpp ? money(result.hpp) : '0'"></span>) dan resep bahan baku ke database. 
                                                Stok bahan baku akan terpotong secara otomatis setiap kali menu ini terjual di Kasir (POS).
                                            </p>
                                        </div>
                                    </div>

                                    <button type="button" @click="save()" :disabled="busy || !result || !result.is_complete" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#f06428] to-[#df5826] text-white font-bold text-sm shadow-md shadow-orange-500/20 hover:opacity-95 transition-all flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i class="fas fa-spinner fa-spin text-sm" x-show="busy"></i>
                                        <i class="fas fa-check text-sm" x-show="!busy"></i>
                                        <span x-text="busy ? 'Menyimpan ke Produk…' : 'Simpan HPP &amp; Resep ke Produk Ini'"></span>
                                    </button>
                                </div>
                            @else
                                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-500 flex items-center gap-3">
                                    <i class="fas fa-info-circle text-gray-400 text-base shrink-0"></i>
                                    <div>
                                        Anda sedang dalam <strong>Mode Simulasi Bebas</strong>. Jika ingin menyimpan formula resep ini ke salah satu menu produk kafe, pilih nama produk pada dropdown di atas lalu klik "Muat".
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- WIZARD NAVIGATION CONTROLS (Sticky Bottom Bar) --}}
                        <div class="mt-6 pt-4 border-t border-gray-200/80 flex items-center justify-between gap-3">
                            <button type="button" 
                                    @click="goTo(step - 1)" 
                                    :disabled="busy || step === 1" 
                                    class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 text-[#111827] font-bold text-xs transition-colors flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Kembali</span>
                            </button>

                            <div class="hidden sm:block text-xs font-semibold text-gray-400">
                                Langkah <span x-text="step" class="text-[#df5826] font-bold"></span> dari 6
                            </div>

                            <button type="submit" 
                                    :disabled="busy || step === 6" 
                                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#f06428] to-[#df5826] text-white font-bold text-xs shadow-md shadow-orange-500/20 hover:opacity-95 transition-all flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                                <i class="fas fa-spinner fa-spin text-xs" x-show="busy"></i>
                                <span x-text="busy ? 'Memproses…' : 'Lanjut ke: ' + (steps[step] || 'Selesai')"></span>
                                <i class="fas fa-arrow-right text-xs" x-show="!busy && step < 6"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- RIGHT COLUMN: REALTIME STICKY LIVE HPP WIDGET (4 Cols) --}}
                <div class="lg:col-span-5 xl:col-span-4 sticky top-[72px] space-y-4">
                    
                    {{-- Realtime HPP Card --}}
                    <div class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.02)] space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-[#df5826] animate-pulse"></div>
                                <h3 class="font-bold text-xs text-[#111827] uppercase tracking-wider">Ringkasan HPP</h3>
                            </div>
                            <button type="button" @click="calculate()" class="text-xs text-[#df5826] hover:underline font-bold flex items-center gap-1" title="Perbarui perhitungan">
                                <i class="fas fa-arrows-rotate text-[10px]" :class="busy ? 'fa-spin' : ''"></i>
                                <span>Hitung</span>
                            </button>
                        </div>

                        {{-- Big HPP per Unit Highlight --}}
                        <div class="bg-gradient-to-br from-[#fff6f0] to-[#fff0e6] border border-[#df5826]/20 rounded-2xl p-4 text-center">
                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Estimasi HPP per Unit</span>
                            <div class="text-2xl md:text-3xl font-extrabold text-[#df5826] mt-1 tracking-tight" 
                                 x-text="result?.hpp ? money(result.hpp) : (estimatedCostPerUnit ? money(estimatedCostPerUnit) : 'Rp 0')">
                            </div>
                            <span class="text-[11px] text-gray-500 font-medium block mt-0.5" 
                                  x-text="'per ' + form.production_unit + ' (Output: ' + form.production_quantity + ' ' + form.production_unit + ')'">
                            </span>
                        </div>

                        {{-- Breakdown Line Items --}}
                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Total HPP 1 Siklus</span>
                                <strong class="text-[#111827]" x-text="result?.total_hpp ? money(result.total_hpp) : money(totalEstimatedBatch)"></strong>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#df5826]"></span>
                                    <span>Bahan Baku &amp; Kemasan</span>
                                </span>
                                <span class="font-bold text-[#111827]" x-text="result ? money(result.ingredients_cost) : money(totalIngredientEstimate)"></span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <span>Biaya Tenaga Kerja</span>
                                </span>
                                <span class="font-bold text-[#111827]" x-text="money(Number(form.labor_cost) || 0)"></span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Biaya Overhead</span>
                                </span>
                                <span class="font-bold text-[#111827]" x-text="result ? money(result.overhead_cost) : money(estimatedOverheadCost)"></span>
                            </div>
                        </div>

                        {{-- Profitability Simulation Section --}}
                        <div class="pt-3 border-t border-gray-100 space-y-2.5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Simulasi Profitabilitas</span>
                            
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Harga Jual Simulasi:</span>
                                <span class="font-bold text-[#111827]" x-text="money(Number(form.selling_price) || 0)"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Estimasi Laba Kotor:</span>
                                <span class="font-bold" :class="estimatedGrossProfit >= 0 ? 'text-[#289e5c]' : 'text-red-600'" x-text="money(estimatedGrossProfit)"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Gross Margin:</span>
                                <span class="font-bold px-2 py-0.5 rounded-full text-[11px]" 
                                      :class="estimatedMarginPercent >= 40 ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'"
                                      x-text="percent(estimatedMarginPercent)">
                                </span>
                            </div>
                        </div>

                        {{-- Completeness Check Indicator --}}
                        <div class="pt-2">
                            <template x-if="result && result.is_complete">
                                <div class="p-2.5 rounded-xl bg-green-50 text-green-800 border border-green-200 text-[11px] font-semibold flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span>Semua komponen biaya terhitung lengkap</span>
                                </div>
                            </template>
                            <template x-if="result && !result.is_complete">
                                <div class="p-2.5 rounded-xl bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-semibold flex items-start gap-2">
                                    <i class="fas fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                                    <span x-text="'Lengkapi harga modal: ' + result.missing_costs.join(', ')"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Quick Tip Card --}}
                    <div class="bg-white border border-[#e6e5e0] rounded-2xl p-4 text-xs text-[#53565a] space-y-2">
                        <div class="flex items-center gap-2 text-[#df5826] font-bold">
                            <i class="fas fa-book-open"></i>
                            <span>Pedoman HPP Kafe</span>
                        </div>
                        <p class="leading-relaxed text-[11px]">
                            Standar food cost kafe &amp; restoran ideal adalah di rentang <strong>28% – 35%</strong> dari harga jual (Margin kotor <strong>65% – 72%</strong>).
                        </p>
                    </div>

                </div>

            </div>

        </main>

        {{-- MODAL: BUAT BAHAN BAKU BARU --}}
        @can('manage-inventory')
        <div x-show="showNewMaterialModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showNewMaterialModal = false"></div>
            
            <div class="relative bg-white rounded-[24px] max-w-lg w-full p-6 shadow-2xl border border-gray-100 z-10 space-y-5" @keydown.escape.window="showNewMaterialModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#df5826] flex items-center justify-center text-sm">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#111827]">Tambah Bahan Baku Baru</h3>
                            <p class="text-[11px] text-gray-400">Buat data bahan dari nota pembelian dan langsung pilih ke resep</p>
                        </div>
                    </div>
                    <button type="button" @click="showNewMaterialModal = false" class="text-gray-400 hover:text-gray-600 p-1">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="new-material-name" class="block text-xs font-bold text-gray-700 mb-1.5">Nama Bahan Baku <span class="text-red-500">*</span></label>
                        <input id="new-material-name" x-model="newMaterial.name" maxlength="255" class="hpp-input" placeholder="Contoh: Fresh Milk Greenfields 1L">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="purchase-total" class="block text-xs font-bold text-gray-700 mb-1.5">Harga Beli Total (Rp) <span class="text-red-500">*</span></label>
                            <input id="purchase-total" x-model="newMaterial.purchase_total" type="number" min="0.01" max="99999999.99" step="0.01" class="hpp-input" placeholder="Contoh: 28000">
                        </div>

                        <div>
                            <label for="purchase-quantity" class="block text-xs font-bold text-gray-700 mb-1.5">Jumlah Beli <span class="text-red-500">*</span></label>
                            <input id="purchase-quantity" x-model="newMaterial.purchase_quantity" type="number" min="0.0001" max="99999999" step="0.0001" class="hpp-input" placeholder="Contoh: 1000">
                        </div>
                    </div>

                    <div>
                        <label for="new-material-unit" class="block text-xs font-bold text-gray-700 mb-1.5">Satuan Pembelian / Stok <span class="text-red-500">*</span></label>
                        <select id="new-material-unit" x-model="newMaterial.unit" class="hpp-input cursor-pointer font-semibold">
                            <template x-for="unit in materialUnits" :key="unit">
                                <option :value="unit" x-text="unit"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Unit Price Calculation Preview --}}
                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 text-xs">
                        <span class="text-gray-400 block text-[11px]">Estimasi Harga per Satuan Bahan:</span>
                        <div class="text-base font-bold text-[#df5826] mt-0.5" 
                             x-text="purchaseUnitCost === null ? 'Masukkan total harga & jumlah beli' : unitMoney(purchaseUnitCost) + ' / ' + newMaterial.unit">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Stok awal akan dibuat 0. Stok fisik dicatat melalui mutasi inventori.</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="showNewMaterialModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="createMaterial(); showNewMaterialModal = false;" :disabled="busy || !newMaterial.name || !purchaseUnitCost" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#f06428] to-[#df5826] text-white text-xs font-bold shadow-sm hover:opacity-95 transition-opacity disabled:opacity-40">
                        Simpan &amp; Pilih ke Resep
                    </button>
                </div>
            </div>
        </div>
        @endcan

    </div>
</div>

<script>
function hppCalculator() {
    const initial = {{ Illuminate\Support\Js::from($initial) }};
    return {
        form: initial,
        materials: {{ Illuminate\Support\Js::from($materials) }},
        step: 1,
        furthest: 1,
        steps: [
            'Target Produksi',
            'Bahan Baku',
            'Tenaga Kerja',
            'Overhead',
            'Hasil & Margin',
            'Pajak & Final'
        ],
        outputUnits: ['porsi', 'pcs', 'cup', 'gram', 'kg', 'ml', 'liter', 'botol', 'pack', 'dus', 'shot', 'slice'],
        materialUnits: {{ Illuminate\Support\Js::from(array_keys(config('inventory.units'))) }},
        result: null,
        busy: false,
        error: '',
        saved: '',
        nextKey: initial.recipes.length,
        materialSearch: '',
        showNewMaterialModal: false,
        newMaterial: {
            name: '',
            purchase_total: '',
            purchase_quantity: '',
            unit: 'ml'
        },

        init() {
            this.form.recipes.forEach((row, index) => row.key = index);
            // Precalculate initial if recipes already exist
            if (this.form.recipes.some(r => r.raw_material_id)) {
                this.calculate();
            }
        },

        invalidate() {
            this.saved = '';
        },

        materialFor(row) {
            return this.materials.find(material => Number(material.id) === Number(row.raw_material_id));
        },

        filteredMaterials(row) {
            return this.materials.filter(material => 
                Number(material.id) === Number(row.raw_material_id) || 
                material.name.toLocaleLowerCase('id').includes(this.materialSearch.toLocaleLowerCase('id'))
            );
        },

        recipeUnits(row) {
            const unit = (this.materialFor(row)?.unit || '').toLowerCase();
            return ['g', 'gram', 'kg'].includes(unit) ? ['gram', 'kg'] : 
                   ['ml', 'l', 'liter'].includes(unit) ? ['ml', 'liter'] : 
                   unit ? [unit] : [];
        },

        rowEstimatedCost(row) {
            const mat = this.materialFor(row);
            if (!mat || !mat.cost_per_unit || !row.amount) return 0;
            let amount = Number(row.amount);
            const from = (row.unit || mat.unit).toLowerCase();
            const to = (mat.unit || '').toLowerCase();
            if ((from === 'kg' && ['g', 'gram'].includes(to)) || (from === 'liter' && to === 'ml')) {
                amount = amount * 1000;
            } else if (['g', 'gram'].includes(from) && to === 'kg') {
                amount = amount / 1000;
            } else if (from === 'ml' && ['l', 'liter'].includes(to)) {
                amount = amount / 1000;
            }
            return Math.round(amount * Number(mat.cost_per_unit) * 100) / 100;
        },

        get totalIngredientEstimate() {
            return this.form.recipes.reduce((sum, r) => sum + this.rowEstimatedCost(r), 0);
        },

        get estimatedOverheadCost() {
            if (this.form.overhead_mode === 'percentage') {
                const base = this.totalIngredientEstimate + (Number(this.form.labor_cost) || 0);
                return Math.round(base * (Number(this.form.overhead_percent) || 0) / 100);
            }
            return Number(this.form.overhead_cost) || 0;
        },

        get totalEstimatedBatch() {
            return this.totalIngredientEstimate + (Number(this.form.labor_cost) || 0) + this.estimatedOverheadCost;
        },

        get estimatedCostPerUnit() {
            const qty = Number(this.form.production_quantity) || 1;
            return qty > 0 ? Math.round(this.totalEstimatedBatch / qty) : 0;
        },

        get estimatedGrossProfit() {
            const selling = Number(this.form.selling_price) || 0;
            const hpp = this.result?.hpp !== null && this.result?.hpp !== undefined ? Number(this.result.hpp) : this.estimatedCostPerUnit;
            return selling - hpp;
        },

        get estimatedMarginPercent() {
            const selling = Number(this.form.selling_price) || 0;
            if (selling <= 0) return 0;
            return Math.round((this.estimatedGrossProfit / selling) * 10000) / 100;
        },

        get purchaseUnitCost() {
            const total = Number(this.newMaterial.purchase_total);
            const quantity = Number(this.newMaterial.purchase_quantity);
            return total > 0 && quantity > 0 ? total / quantity : null;
        },

        money(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(value || 0);
        },

        unitMoney(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 6
            }).format(value || 0);
        },

        percent(value) {
            return value === null || value === undefined ? '—' : 
                   new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(value) + '%';
        },

        addRow() {
            this.form.recipes.push({
                key: this.nextKey++,
                raw_material_id: '',
                amount: 1,
                unit: '',
                notes: ''
            });
            this.invalidate();
        },

        removeRow(index) {
            this.form.recipes.splice(index, 1);
            this.invalidate();
        },

        validateStep() {
            const stepEl = document.getElementById('hpp-step-' + this.step);
            if (!stepEl) return true;
            const inputs = stepEl.querySelectorAll('input,select,textarea');
            for (const input of inputs) {
                if (!input.disabled && !input.reportValidity()) return false;
            }
            return true;
        },

        goTo(step) {
            if (!this.busy && step >= 1 && step <= this.furthest) {
                this.step = step;
            }
        },

        hasValidRecipes() {
            return Array.isArray(this.form.recipes) && this.form.recipes.some(r => Boolean(r.raw_material_id) && Number(r.amount) > 0);
        },

        async next() {
            if (this.busy) return;
            this.error = '';
            
            // Validasi per langkah
            if (this.step === 1) {
                if (!this.form.production_quantity || Number(this.form.production_quantity) <= 0) {
                    this.error = 'Silakan isi jumlah hasil produksi per siklus (minimal 0.01).';
                    return;
                }
                if (this.form.selling_price === '' || Number(this.form.selling_price) < 0) {
                    this.error = 'Silakan isi simulasi harga jual per unit.';
                    return;
                }
            }
            
            if (this.step === 2) {
                const validCount = this.form.recipes.filter(r => Boolean(r.raw_material_id) && Number(r.amount) > 0).length;
                if (validCount === 0) {
                    this.error = 'Silakan pilih minimal 1 bahan baku inventori dan masukkan takaran resep sebelum lanjut ke langkah berikutnya.';
                    return;
                }
            }
            
            if (!this.validateStep()) return;
            
            // Jika berada di step 4 ke atas dan bahan valid, sinkronkan via API
            if (this.step >= 4 && this.hasValidRecipes()) {
                if (!(await this.calculate())) return;
            }
            
            if (this.step < 6) {
                this.step++;
                this.furthest = Math.max(this.furthest, this.step);
                // Hitung otomatis jika berada di langkah hasil akhir (Langkah 5 atau 6)
                if (this.step >= 5 && this.hasValidRecipes()) {
                    this.calculate();
                }
            }
        },

        payload() {
            const validRecipes = this.form.recipes
                .filter(r => Boolean(r.raw_material_id) && Number(r.amount) > 0)
                .map(({raw_material_id, amount, unit, notes}) => ({
                    raw_material_id: Number(raw_material_id),
                    amount: Number(amount),
                    unit: unit || '',
                    notes: notes || ''
                }));
                
            return {
                ...this.form,
                recipes: validRecipes.length > 0 ? validRecipes : this.form.recipes,
                target_margin: this.form.target_margin === '' ? null : this.form.target_margin,
                target_markup: this.form.target_markup === '' ? null : this.form.target_markup
            };
        },

        async request(url, method, payload) {
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : data.message || 'Permintaan gagal. Silakan coba lagi.');
            }
            return data;
        },

        showError(error) {
            let msg = error.message || 'Terjadi kendala saat memproses perhitungan.';
            // Bersihkan format teknis jika ada
            msg = msg.replace(/The recipes\.\d+\.raw_material_id field is required\./gi, 'Bahan baku pada baris resep wajib dipilih.')
                     .replace(/The recipes\.\d+\.amount field is required\./gi, 'Jumlah takaran bahan wajib diisi.')
                     .replace(/recipes\.\d+\.raw_material_id/gi, 'Bahan baku')
                     .replace(/recipes\.\d+\.amount/gi, 'Jumlah takaran')
                     .replace(/field is required\./gi, 'wajib diisi.')
                     .replace(/The /gi, '')
                     .replace(/is required\./gi, 'wajib diisi.');
            this.error = msg;
            this.$nextTick(() => {
                const el = this.$refs.errorBox || document.getElementById('hpp-error-banner');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        },

        async send(url, method) {
            if (this.busy) return false;
            this.busy = true;
            this.error = '';
            this.saved = '';
            try {
                const data = await this.request(url, method, this.payload());
                this.result = data.result;
                this.saved = data.message || '';
                return true;
            } catch (error) {
                this.result = null;
                this.showError(error);
                return false;
            } finally {
                this.busy = false;
            }
        },

        calculate() {
            if (!this.hasValidRecipes()) {
                this.error = 'Silakan pilih bahan baku dan tentukan takaran pada Langkah 2 terlebih dahulu.';
                return Promise.resolve(false);
            }
            return this.send({{ Illuminate\Support\Js::from(route('admin.hpp.calculate')) }}, 'POST');
        },

        save() {
            return this.send({{ Illuminate\Support\Js::from($product ? route('admin.hpp.update', $product->id) : null) }}, 'PUT');
        },

        async createMaterial() {
            if (this.busy) return;
            this.busy = true;
            this.error = '';
            try {
                const data = await this.request({{ Illuminate\Support\Js::from(route('admin.hpp.materials.store')) }}, 'POST', this.newMaterial);
                this.materials.push(data.material);
                let row = this.form.recipes.find(row => !row.raw_material_id);
                if (!row) {
                    if (this.form.recipes.length >= 100) {
                        this.saved = 'Bahan tersimpan. Hapus satu baris sebelum memilihnya.';
                        return;
                    }
                    this.addRow();
                    row = this.form.recipes[this.form.recipes.length - 1];
                }
                row.raw_material_id = data.material.id;
                row.unit = '';
                this.materialSearch = '';
                this.invalidate();
                this.saved = 'Bahan baku baru berhasil disimpan dan langsung dipilih pada resep.';
                this.newMaterial = { name: '', purchase_total: '', purchase_quantity: '', unit: 'ml' };
                this.calculate();
            } catch (error) {
                this.showError(error);
            } finally {
                this.busy = false;
            }
        }
    };
}
</script>
</body>
</html>
