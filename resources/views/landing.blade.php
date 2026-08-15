<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>Science Food Festival — Learn Science, Create Experience</title>
    <meta name="description" content="Science Food Festival adalah program tahunan pembelajaran IPA berbasis proyek (Project-Based Learning) melalui eksplorasi makanan, gizi, dan sistem pencernaan manusia.">
    <meta name="theme-color" content="#11998E">
    <meta property="og:title" content="Science Food Festival">
    <meta property="og:description" content="Belajar IPA Lewat Makanan Sehat & Kreatif — Program Tahunan Project-Based Learning.">
    <meta property="og:image" content="{{ asset('foto/hero.jpg') }}">
    <meta property="og:type" content="website">

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Custom CSS Design System & Motion System -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <!-- Tailwind Custom Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            teal: '#11998E',       // Primary Teal
                            'teal-dark': '#0B6E64',
                            'teal-light': '#CCFBF1',
                            yellow: '#FFC145',     // Accent Golden Yellow
                            'yellow-hover': '#EAB308',
                            dark: '#1F2937',       // Dark Gray
                            'dark-subtle': '#2E2E2E',
                            light: '#F9FAFB',      // Off-white
                        }
                    },
                    boxShadow: {
                        'soft': '0 20px 40px -15px rgba(17, 153, 142, 0.18)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 2px 6px -2px rgba(0, 0, 0, 0.02)',
                        'glow': '0 0 25px rgba(17, 153, 142, 0.35)',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans text-brand-dark bg-white antialiased overflow-x-hidden selection:bg-brand-teal selection:text-white">

    <!-- NAVBAR -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-gray-100/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo/Brand -->
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer group" onclick="window.scrollTo({top:0, behavior:'smooth'})">
                    <div class="w-10 h-10 bg-brand-teal rounded-2xl flex items-center justify-center text-white shadow-soft group-hover:scale-105 transition-transform duration-300 shrink-0">
                        <i class="ph-bold ph-flask text-xl"></i>
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="font-black text-base sm:text-lg tracking-tight text-brand-teal leading-none">Science<span class="text-brand-yellow">.</span></span>
                        <span class="font-extrabold text-[11px] sm:text-xs tracking-wider text-brand-dark uppercase mt-0.5 leading-none">Food Festival</span>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-gray-600 hover:text-brand-teal font-medium text-sm transition-colors duration-200">About</a>
                    <a href="#program" class="text-gray-600 hover:text-brand-teal font-medium text-sm transition-colors duration-200">Program</a>
                    <a href="#experience" class="text-gray-600 hover:text-brand-teal font-medium text-sm transition-colors duration-200">Experience</a>
                    <a href="#gallery" class="text-gray-600 hover:text-brand-teal font-medium text-sm transition-colors duration-200">Gallery</a>
                    <a href="#faq" class="text-gray-600 hover:text-brand-teal font-medium text-sm transition-colors duration-200">FAQ</a>
                    
                    <!-- Dynamic Academic Year Tag in Navbar -->
                    <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-brand-teal-light/60 border border-brand-teal/20 text-brand-teal text-xs font-semibold">
                        <i class="ph-fill ph-calendar-blank text-brand-teal"></i>
                        <span>TA <span data-academic-year data-academic-year-format="short">2026/2027</span></span>
                    </div>

                    <a href="/login" class="bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold text-sm px-6 py-2.5 rounded-full transition-all duration-300 hover:scale-105 shadow-md hover:shadow-soft">
                        DAFTAR
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" aria-label="Buka Menu" class="text-gray-700 hover:text-brand-teal focus:outline-none p-2 rounded-xl hover:bg-gray-100 transition-colors">
                        <i class="ph ph-list text-3xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-lg border-t border-gray-100 absolute w-full shadow-2xl transition-all duration-300">
            <div class="px-4 pt-3 pb-6 space-y-2 flex flex-col text-center">
                <a href="#about" class="block px-4 py-3 text-gray-700 font-medium hover:bg-brand-teal-light rounded-xl transition-colors">About</a>
                <a href="#program" class="block px-4 py-3 text-gray-700 font-medium hover:bg-brand-teal-light rounded-xl transition-colors">Program</a>
                <a href="#experience" class="block px-4 py-3 text-gray-700 font-medium hover:bg-brand-teal-light rounded-xl transition-colors">Experience</a>
                <a href="#gallery" class="block px-4 py-3 text-gray-700 font-medium hover:bg-brand-teal-light rounded-xl transition-colors">Gallery</a>
                <a href="#faq" class="block px-4 py-3 text-gray-700 font-medium hover:bg-brand-teal-light rounded-xl transition-colors">FAQ</a>
                
                <div class="py-2">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-teal-light border border-brand-teal/20 text-brand-teal font-semibold text-xs">
                        <i class="ph-fill ph-sparkle text-brand-yellow"></i>
                        <span>Tahun Ajaran <strong data-academic-year>2026 / 2027</strong></span>
                    </div>
                </div>

                <a href="/login" class="block mt-2 bg-brand-yellow text-brand-dark font-bold px-4 py-3.5 rounded-xl mx-2 shadow-md hover:bg-brand-yellow-hover transition-colors">
                    DAFTAR SEKARANG
                </a>
            </div>
        </div>
    </nav>

    <!-- 1. HERO SECTION -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-brand-light bg-grid">
        <!-- Parallax Background Decorative Lights -->
        <div class="parallax-bg absolute top-20 right-0 w-[500px] h-[500px] bg-brand-teal-light rounded-full blur-[110px] opacity-70 -z-10 pointer-events-none" data-speed="0.25"></div>
        <div class="parallax-bg absolute bottom-0 left-[-10%] w-[400px] h-[400px] bg-yellow-100 rounded-full blur-[90px] opacity-70 -z-10 pointer-events-none" data-speed="-0.15"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                
                <!-- Hero Text -->
                <div class="text-center lg:text-left">
                    <!-- Dynamic Academic Year Badge -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white shadow-sm border border-brand-teal/20 text-brand-teal font-semibold text-sm mb-6 fade-up">
                        <i class="ph-fill ph-sparkle text-brand-yellow text-lg"></i>
                        <span>Program Tahunan • TA <strong data-academic-year class="text-brand-teal-dark">2026 / 2027</strong></span>
                    </div>

                    <h1 class="text-fluid-h1 font-black text-brand-dark mb-6 tracking-tight fade-up" data-delay="100">
                        SCIENCE FOOD <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-teal to-emerald-600">FESTIVAL</span>
                    </h1>
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-700 mb-6 fade-up" data-delay="150">
                        Belajar IPA Lewat Makanan Sehat & Kreatif
                    </h2>
                    <p class="text-fluid-p text-gray-500 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed fade-up" data-delay="200">
                        Science Food Festival merupakan program pembelajaran berbasis proyek yang mengajak siswa memahami ilmu IPA melalui eksplorasi makanan, gizi, dan sistem pencernaan manusia dalam pengalaman belajar yang kreatif dan menyenangkan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start fade-up" data-delay="250">
                        <a href="/login" class="btn-cta-primary bg-brand-teal text-white font-bold text-lg px-8 py-4 rounded-full shadow-[0_10px_25px_rgba(17,153,142,0.35)] flex items-center justify-center gap-2 group">
                            Daftar Sekarang <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="#about" class="bg-white hover:bg-gray-50 text-brand-dark font-bold text-lg px-8 py-4 rounded-full shadow-card border border-gray-100 transition-all hover:-translate-y-0.5 flex items-center justify-center">
                            Kenali Program
                        </a>
                    </div>
                </div>

                <!-- Hero Image/Visual (Priority 1 Load) -->
                <div class="relative mx-auto w-full max-w-lg lg:max-w-none fade-up" data-delay="300">
                    <div class="relative w-full aspect-square md:w-[460px] md:h-[460px] mx-auto z-10">
                        <div class="absolute inset-0 bg-brand-yellow blob-shape transform rotate-12 scale-105 -z-10 opacity-90"></div>
                        
                        <!-- Priority 1 Hero Image -->
                        <img src="{{ isset($heroPhoto) && $heroPhoto ? $heroPhoto->url : asset('foto/hero.jpg') }}" 
                             alt="{{ isset($heroPhoto) && $heroPhoto ? $heroPhoto->title : 'Science Food Festival Kegiatan Utama' }}" 
                             width="460" 
                             height="460"
                             fetchpriority="high"
                             loading="eager" 
                             decoding="async" 
                             class="img-priority w-full h-full object-cover blob-shape shadow-2xl border-4 border-white"
                             onerror="this.src='https://placehold.co/800x800/11998E/ffffff?text=FOTO+KEGIATAN'">
                        
                        <!-- Floating Parallax Badge 1 -->
                        <div class="parallax-element absolute -top-6 -right-4 sm:-right-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-xl flex items-center gap-3 border border-gray-100" data-speed="0.08">
                            <div class="bg-green-100 p-2.5 rounded-xl text-green-600">
                                <i class="ph-fill ph-leaf text-2xl"></i>
                            </div>
                            <div class="font-bold text-sm text-gray-800">Gizi<br><span class="text-green-600 font-semibold">Seimbang</span></div>
                        </div>
                        
                        <!-- Floating Parallax Badge 2 -->
                        <div class="parallax-element absolute -bottom-8 -left-4 sm:-left-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-xl flex items-center gap-3 border border-gray-100" data-speed="-0.08">
                            <div class="bg-brand-teal-light p-2.5 rounded-xl text-brand-teal">
                                <i class="ph-fill ph-dna text-2xl"></i>
                            </div>
                            <div class="font-bold text-sm text-gray-800">Sains<br><span class="text-brand-teal font-semibold">Aplikatif</span></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. ABOUT THE PROGRAM -->
    <section id="about" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- About Image (Slide Right, Smart Skeleton) -->
                <div class="order-2 lg:order-1 relative fade-right">
                    <div class="skeleton-container rounded-3xl shadow-soft w-full aspect-[4/3]">
                        <div class="skeleton-shimmer"></div>
                        <img src="{{ isset($aboutPhoto) && $aboutPhoto ? $aboutPhoto->url : asset('foto/about.jpg') }}" 
                             alt="{{ isset($aboutPhoto) && $aboutPhoto ? $aboutPhoto->title : 'Tentang Program Science Food Festival' }}" 
                             width="800" 
                             height="600" 
                             loading="lazy" 
                             decoding="async" 
                             class="img-smart w-full h-full object-cover rounded-3xl"
                             onerror="this.src='https://placehold.co/800x600/FFC145/11998E?text=FOTO+PROGRAM'">
                    </div>

                    <div class="absolute -bottom-8 -right-8 bg-brand-teal text-white p-7 rounded-3xl shadow-xl hidden md:flex items-center gap-4 zoom-in" data-delay="200">
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-white shrink-0">
                            <i class="ph-fill ph-student text-3xl"></i>
                        </div>
                        <div>
                            <p class="font-extrabold text-xl leading-tight">Project-Based</p>
                            <p class="text-brand-teal-light text-sm">Learning Experience</p>
                        </div>
                    </div>
                </div>

                <!-- About Text (Slide Left) -->
                <div class="order-1 lg:order-2 fade-left" data-delay="100">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-teal-light text-brand-teal font-bold text-xs uppercase tracking-wider mb-4">
                        <i class="ph-fill ph-info"></i> About The Program
                    </div>
                    <h2 class="text-fluid-h2 font-extrabold text-brand-dark mb-6 leading-tight">
                        Apa Itu Science Food Festival?
                    </h2>
                    <div class="space-y-4 text-fluid-p text-gray-600 leading-relaxed">
                        <p>
                            <strong class="text-brand-teal">Science Food Festival</strong> adalah program pembelajaran IPA yang dirancang untuk mengubah konsep pembelajaran di dalam kelas menjadi pengalaman nyata.
                        </p>
                        <p>
                            Melalui kegiatan ini, peserta bekerja dalam kelompok untuk mengeksplorasi makanan bergizi, memahami kandungan zat makanan, mempelajari sistem pencernaan manusia, menyusun informasi ilmiah, serta menyajikan hasil pembelajarannya melalui pameran dan presentasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. WHY SCIENCE FOOD FESTIVAL? -->
    <section id="program" class="py-24 bg-brand-light relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 fade-up">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-teal-light text-brand-teal font-bold text-xs uppercase tracking-wider mb-3">
                    Why Program
                </div>
                <h2 class="text-fluid-h2 font-extrabold text-brand-dark">Bukan Sekadar Festival Makanan Biasa</h2>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl p-8 shadow-card hover:shadow-soft transition-all duration-300 transform hover:-translate-y-2 fade-up" data-delay="100">
                    <div class="w-14 h-14 bg-brand-teal-light text-brand-teal rounded-2xl flex items-center justify-center mb-6">
                        <i class="ph-fill ph-brain text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-brand-dark mb-3">Belajar Lebih Bermakna</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Konsep IPA tidak hanya dipelajari melalui buku, tetapi dikaitkan dengan kehidupan sehari-hari.</p>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white rounded-3xl p-8 shadow-card hover:shadow-soft transition-all duration-300 transform hover:-translate-y-2 fade-up" data-delay="200">
                    <div class="w-14 h-14 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ph-fill ph-cooking-pot text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-brand-dark mb-3">Dari Hal Terdekat</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Makanan merupakan bagian dari kehidupan sehari-hari sehingga konsep sains lebih mudah dipahami.</p>
                </div>
                
                <!-- Card 3 -->
                <div class="bg-white rounded-3xl p-8 shadow-card hover:shadow-soft transition-all duration-300 transform hover:-translate-y-2 fade-up" data-delay="300">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ph-fill ph-users-three text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-brand-dark mb-3">Kolaborasi</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Peserta belajar membagi tugas, berkomunikasi, berdiskusi, dan bekerja sebagai tim.</p>
                </div>
                
                <!-- Card 4 -->
                <div class="bg-white rounded-3xl p-8 shadow-card hover:shadow-soft transition-all duration-300 transform hover:-translate-y-2 fade-up" data-delay="400">
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="ph-fill ph-palette text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-brand-dark mb-3">Kreativitas</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Peserta tidak hanya menjawab soal, tetapi menghasilkan karya dan mempresentasikan pengetahuannya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. WHAT WILL STUDENTS DO? (Timeline Journey) -->
    <section class="py-24 bg-white relative">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-up">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-teal-light text-brand-teal font-bold text-xs uppercase tracking-wider mb-3">
                    Student Journey
                </div>
                <h2 class="text-fluid-h2 font-extrabold text-brand-dark">Apa yang Dilakukan Peserta?</h2>
            </div>

            <div class="relative">
                <!-- Vertical Line (Left-aligned on mobile, Centered on desktop) -->
                <div class="absolute left-6 md:left-1/2 top-0 bottom-0 w-1 bg-gray-100 transform -translate-x-1/2 rounded-full"></div>

                <div class="space-y-12">
                    <!-- Step 1 -->
                    <div class="relative flex flex-col md:flex-row items-start md:items-center md:justify-between group zoom-in pl-16 md:pl-0" data-delay="100">
                        <div class="w-full md:w-5/12 text-left md:text-right md:pr-8 mb-0">
                            <h4 class="text-xl sm:text-2xl font-bold text-brand-dark mb-1.5 sm:mb-2">01 — Explore</h4>
                            <p class="text-gray-500 text-sm sm:text-base leading-relaxed">Peserta mempelajari makanan, zat gizi, dan sistem pencernaan manusia.</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 absolute left-6 md:left-1/2 top-0 md:top-1/2 transform -translate-x-1/2 md:-translate-y-1/2 bg-white border-4 border-brand-teal rounded-full flex items-center justify-center z-10 shadow-md group-hover:scale-110 group-hover:bg-brand-teal transition-all">
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-brand-teal rounded-full group-hover:bg-white transition-colors"></div>
                        </div>
                        <div class="w-full md:w-5/12 hidden md:block pl-8"></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative flex flex-col md:flex-row items-start md:items-center md:justify-between group zoom-in pl-16 md:pl-0" data-delay="200">
                        <div class="w-full md:w-5/12 hidden md:block pr-8"></div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 absolute left-6 md:left-1/2 top-0 md:top-1/2 transform -translate-x-1/2 md:-translate-y-1/2 bg-white border-4 border-brand-yellow rounded-full flex items-center justify-center z-10 shadow-md group-hover:scale-110 group-hover:bg-brand-yellow transition-all">
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-brand-yellow rounded-full group-hover:bg-white transition-colors"></div>
                        </div>
                        <div class="w-full md:w-5/12 text-left md:pl-8 mb-0">
                            <h4 class="text-xl sm:text-2xl font-bold text-brand-dark mb-1.5 sm:mb-2">02 — Plan</h4>
                            <p class="text-gray-500 text-sm sm:text-base leading-relaxed">Peserta menentukan konsep makanan dan membagi tugas dalam kelompok.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative flex flex-col md:flex-row items-start md:items-center md:justify-between group zoom-in pl-16 md:pl-0" data-delay="300">
                        <div class="w-full md:w-5/12 text-left md:text-right md:pr-8 mb-0">
                            <h4 class="text-xl sm:text-2xl font-bold text-brand-dark mb-1.5 sm:mb-2">03 — Create</h4>
                            <p class="text-gray-500 text-sm sm:text-base leading-relaxed">Peserta menyiapkan makanan dan membuat media visual/poster edukasi.</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 absolute left-6 md:left-1/2 top-0 md:top-1/2 transform -translate-x-1/2 md:-translate-y-1/2 bg-white border-4 border-brand-teal rounded-full flex items-center justify-center z-10 shadow-md group-hover:scale-110 group-hover:bg-brand-teal transition-all">
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-brand-teal rounded-full group-hover:bg-white transition-colors"></div>
                        </div>
                        <div class="w-full md:w-5/12 hidden md:block pl-8"></div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative flex flex-col md:flex-row items-start md:items-center md:justify-between group zoom-in pl-16 md:pl-0" data-delay="400">
                        <div class="w-full md:w-5/12 hidden md:block pr-8"></div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 absolute left-6 md:left-1/2 top-0 md:top-1/2 transform -translate-x-1/2 md:-translate-y-1/2 bg-white border-4 border-brand-yellow rounded-full flex items-center justify-center z-10 shadow-md group-hover:scale-110 group-hover:bg-brand-yellow transition-all">
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-brand-yellow rounded-full group-hover:bg-white transition-colors"></div>
                        </div>
                        <div class="w-full md:w-5/12 text-left md:pl-8 mb-0">
                            <h4 class="text-xl sm:text-2xl font-bold text-brand-dark mb-1.5 sm:mb-2">04 — Present</h4>
                            <p class="text-gray-500 text-sm sm:text-base leading-relaxed">Peserta menjelaskan hasil eksplorasi dan menghubungkannya dengan konsep IPA.</p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="relative flex flex-col md:flex-row items-start md:items-center md:justify-between group zoom-in pl-16 md:pl-0" data-delay="500">
                        <div class="w-full md:w-5/12 text-left md:text-right md:pr-8 mb-0">
                            <h4 class="text-xl sm:text-2xl font-bold text-brand-dark mb-1.5 sm:mb-2">05 — Experience</h4>
                            <p class="text-gray-500 text-sm sm:text-base leading-relaxed">Peserta mendapatkan pengalaman belajar secara nyata melalui pameran dan interaksi langsung.</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 absolute left-6 md:left-1/2 top-0 md:top-1/2 transform -translate-x-1/2 md:-translate-y-1/2 bg-white border-4 border-brand-teal rounded-full flex items-center justify-center z-10 shadow-md group-hover:scale-110 group-hover:bg-brand-teal transition-all">
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-brand-teal rounded-full group-hover:bg-white transition-colors"></div>
                        </div>
                        <div class="w-full md:w-5/12 hidden md:block pl-8"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. SCIENCE BEHIND THE FOOD -->
    <section class="py-24 bg-brand-teal relative overflow-hidden text-white">
        <div class="parallax-bg absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmZmZmYiLz48L3N2Zz4=')]" data-speed="0.1"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 fade-up">
                <h2 class="text-fluid-h2 font-extrabold text-white mb-4">Di Balik Setiap Makanan, Ada Sains</h2>
                <p class="text-brand-teal-light text-lg">Setiap hidangan yang disajikan mengandung makna ilmiah yang dipelajari peserta secara mendalam.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="glass-dark rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 fade-up" data-delay="100">
                    <h4 class="text-xl font-bold mb-2 flex items-center gap-2"><i class="ph-fill ph-grains text-brand-yellow"></i> Karbohidrat</h4>
                    <p class="text-white/80 text-sm leading-relaxed">Sumber energi utama tubuh untuk aktivitas sehari-hari.</p>
                </div>
                <!-- Card 2 -->
                <div class="glass-dark rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 fade-up" data-delay="200">
                    <h4 class="text-xl font-bold mb-2 flex items-center gap-2"><i class="ph-fill ph-barbell text-brand-yellow"></i> Protein</h4>
                    <p class="text-white/80 text-sm leading-relaxed">Mendukung pertumbuhan, perbaikan jaringan, dan regenerasi sel.</p>
                </div>
                <!-- Card 3 -->
                <div class="glass-dark rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 fade-up" data-delay="300">
                    <h4 class="text-xl font-bold mb-2 flex items-center gap-2"><i class="ph-fill ph-drop text-brand-yellow"></i> Lemak</h4>
                    <p class="text-white/80 text-sm leading-relaxed">Cadangan energi dan membantu melindungi serta mendukung fungsi tubuh tertentu.</p>
                </div>
                <!-- Card 4 -->
                <div class="glass-dark rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 md:col-span-2 lg:col-span-1 fade-up" data-delay="400">
                    <h4 class="text-xl font-bold mb-2 flex items-center gap-2"><i class="ph-fill ph-orange-slice text-brand-yellow"></i> Vitamin & Mineral</h4>
                    <p class="text-white/80 text-sm leading-relaxed">Mendukung sistem imun dan berbagai fungsi metabolisme tubuh.</p>
                </div>
                <!-- Card 5 -->
                <div class="glass-dark rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 md:col-span-2 lg:col-span-2 bg-gradient-to-r from-brand-teal-dark to-transparent fade-up" data-delay="500">
                    <h4 class="text-xl font-bold mb-2 flex items-center gap-2"><i class="ph-fill ph-activity text-brand-yellow"></i> Sistem Pencernaan</h4>
                    <p class="text-white/80 text-sm leading-relaxed">Bagaimana makanan diproses oleh organ tubuh secara mekanik dan kimiawi, dari mulut hingga zat gizi akhirnya dapat diserap ke seluruh tubuh.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. THE FESTIVAL EXPERIENCE -->
    <section id="experience" class="py-24 bg-brand-light relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 fade-up">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-teal-light text-brand-teal font-bold text-xs uppercase tracking-wider mb-3">
                    Experience The Festival
                </div>
                <h2 class="text-fluid-h2 font-extrabold text-brand-dark">Bentuk Kegiatan Pameran</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-6 lg:gap-8">
                <!-- Visual Card 1 -->
                <div class="bg-white rounded-3xl p-7 flex items-start gap-6 shadow-card hover:shadow-soft transition-all duration-300 border border-gray-50/80 fade-up" data-delay="100">
                    <div class="bg-orange-100 text-orange-500 p-4 rounded-2xl text-4xl shrink-0">🍽️</div>
                    <div>
                        <h4 class="text-2xl font-bold text-brand-dark mb-2">Food Exhibition</h4>
                        <p class="text-gray-500 leading-relaxed text-sm">Pameran stan makanan bergizi hasil eksplorasi mandiri setiap kelompok.</p>
                    </div>
                </div>
                <!-- Visual Card 2 -->
                <div class="bg-white rounded-3xl p-7 flex items-start gap-6 shadow-card hover:shadow-soft transition-all duration-300 border border-gray-50/80 fade-up" data-delay="200">
                    <div class="bg-blue-100 text-blue-500 p-4 rounded-2xl text-4xl shrink-0">🧪</div>
                    <div>
                        <h4 class="text-2xl font-bold text-brand-dark mb-2">Science Presentation</h4>
                        <p class="text-gray-500 leading-relaxed text-sm">Sesi presentasi di mana peserta menjelaskan hubungan sajian makanannya dengan konsep IPA.</p>
                    </div>
                </div>
                <!-- Visual Card 3 -->
                <div class="bg-white rounded-3xl p-7 flex items-start gap-6 shadow-card hover:shadow-soft transition-all duration-300 border border-gray-50/80 fade-up" data-delay="300">
                    <div class="bg-pink-100 text-pink-500 p-4 rounded-2xl text-4xl shrink-0">🎨</div>
                    <div>
                        <h4 class="text-2xl font-bold text-brand-dark mb-2">Creative Showcase</h4>
                        <p class="text-gray-500 leading-relaxed text-sm">Pameran poster visual infografis dan media edukasi yang dirancang sekreatif mungkin oleh peserta.</p>
                    </div>
                </div>
                <!-- Visual Card 4 -->
                <div class="bg-white rounded-3xl p-7 flex items-start gap-6 shadow-card hover:shadow-soft transition-all duration-300 border border-gray-50/80 fade-up" data-delay="400">
                    <div class="bg-green-100 text-green-500 p-4 rounded-2xl text-4xl shrink-0">👥</div>
                    <div>
                        <h4 class="text-2xl font-bold text-brand-dark mb-2">Collaborative Learning</h4>
                        <p class="text-gray-500 leading-relaxed text-sm">Pengalaman bekerja lintas fungsi (chef, presenter, desainer, peneliti) dan belajar bersama dalam satu tim.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. GALLERY -->
    <section id="gallery" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 fade-up">
                <div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-teal-light text-brand-teal font-bold text-xs uppercase tracking-wider mb-3">
                        Gallery Showcase
                    </div>
                    <h2 class="text-fluid-h2 font-extrabold text-brand-dark">A Glimpse of Our Festival</h2>
                </div>
                <!-- Filter Chips -->
                <div class="flex items-center gap-2 mt-6 md:mt-0 overflow-x-auto pb-2 w-full md:w-auto hide-scrollbar">
                    <button data-filter="all" class="px-5 py-2 bg-brand-teal text-white rounded-full text-sm font-semibold whitespace-nowrap shadow-sm cursor-pointer border border-brand-teal">All</button>
                    <button data-filter="preparation" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full text-sm font-semibold whitespace-nowrap transition-colors cursor-pointer border border-transparent">Preparation</button>
                    <button data-filter="exhibition" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full text-sm font-semibold whitespace-nowrap transition-colors cursor-pointer border border-transparent">Exhibition</button>
                    <button data-filter="presentation" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full text-sm font-semibold whitespace-nowrap transition-colors cursor-pointer border border-transparent">Presentation</button>
                </div>
            </div>

            <!-- Dynamic Gallery Grid Layout -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @forelse($galleryPhotos as $index => $photo)
                    @if($index === 0)
                        <!-- Gallery Item 1 (Featured Big) -->
                        <div class="col-span-2 row-span-2 gallery-card group relative rounded-3xl overflow-hidden cursor-pointer shadow-card zoom-in min-h-[220px] md:min-h-0" 
                             data-category="{{ $photo->category ?? 'exhibition' }}" 
                             data-delay="100">
                            <img src="{{ $photo->url }}" 
                                 alt="{{ $photo->title }}" 
                                 width="600" 
                                 height="600" 
                                 fetchpriority="high"
                                 loading="eager" 
                                 decoding="async" 
                                 class="img-priority gallery-img w-full h-full object-cover transition-transform duration-700 group-hover:scale-108"
                                 onerror="this.src='https://placehold.co/600x600/EAB308/ffffff?text=FOTO+1'">
                        </div>
                    @else
                        <!-- Gallery Item -->
                        <div class="gallery-card group relative rounded-3xl overflow-hidden cursor-pointer shadow-card zoom-in aspect-square md:aspect-auto h-48 md:h-64" 
                             data-category="{{ $photo->category ?? 'exhibition' }}" 
                             data-delay="{{ ($index + 1) * 100 }}">
                            <img src="{{ $photo->url }}" 
                                 alt="{{ $photo->title }}" 
                                 width="400" 
                                 height="400" 
                                 loading="lazy" 
                                 decoding="async" 
                                 class="img-smart gallery-img w-full h-full object-cover transition-transform duration-700 group-hover:scale-108"
                                 onerror="this.src='https://placehold.co/400x400/11998E/ffffff?text=FOTO'">
                        </div>
                    @endif
                @empty
                    <!-- Fallback Static Items if DB is Empty -->
                    <div class="col-span-2 row-span-2 gallery-card group relative rounded-3xl overflow-hidden cursor-pointer shadow-card zoom-in min-h-[220px] md:min-h-0" data-category="exhibition" data-delay="100">
                        <img src="{{ asset('foto/gallery-1.jpg') }}" alt="Exhibition Day Science Food Festival 1" class="gallery-img w-full h-full object-cover">
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 8. ANNUAL PROGRAM (Auto Academic Year Timeline Highlight) -->
    <section class="py-20 border-t border-gray-100 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-teal-light text-brand-teal font-bold text-xs uppercase tracking-wider mb-3 fade-up">
                Annual Program Timeline
            </div>
            <h2 class="text-fluid-h2 font-bold text-brand-dark mb-4 fade-up" data-delay="100">A Program That Grows Every Year</h2>
            <p class="text-gray-500 max-w-2xl mx-auto mb-12 text-fluid-p fade-up" data-delay="150">
                Science Food Festival dirancang sebagai program berkelanjutan yang terus dikembangkan dari tahun ke tahun melalui pengalaman, karya, dan dokumentasi peserta.
            </p>
            
            <!-- Dynamic Academic Timeline Managed by JS -->
            <div id="academic-timeline" class="flex flex-col md:flex-row justify-center items-center gap-8 md:gap-6 relative fade-up" data-delay="200">
                <!-- Connector Line Desktop -->
                <div class="hidden md:block absolute top-1/2 left-1/4 right-1/4 h-0.5 bg-gray-200 -z-10"></div>
                
                <!-- Timeline Card 2025 -->
                <div class="timeline-card bg-white text-brand-dark w-full md:w-48 p-6 rounded-2xl shadow-card border border-gray-100 transition-all duration-300" data-year="2025">
                    <h3 class="text-3xl font-extrabold mb-1">2025</h3>
                    <p class="timeline-status text-gray-500 text-sm font-medium">First Edition</p>
                </div>

                <!-- Timeline Card 2026 -->
                <div class="timeline-card bg-brand-teal text-white w-full md:w-52 p-6 rounded-2xl shadow-lg border border-brand-teal scale-105 transition-all duration-300" data-year="2026">
                    <h3 class="text-3xl font-extrabold mb-1">2026</h3>
                    <p class="timeline-status text-brand-teal-light text-sm font-semibold">Tahun Ajaran Aktif</p>
                </div>

                <!-- Timeline Card 2027 -->
                <div class="timeline-card bg-brand-light text-gray-400 w-full md:w-48 p-6 rounded-2xl border border-dashed border-gray-300 transition-all duration-300" data-year="2027">
                    <h3 class="text-3xl font-extrabold mb-1">2027</h3>
                    <p class="timeline-status text-gray-400 text-sm font-medium">Coming Soon</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. PROGRAM VALUES -->
    <section class="py-24 bg-brand-dark text-white relative overflow-hidden">
        <div class="parallax-bg absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiM0YjU1NjMiLz48L3N2Zz4=')] opacity-20" data-speed="-0.1"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-black mb-16 text-transparent bg-clip-text bg-gradient-to-r from-brand-yellow via-teal-200 to-white fade-up">
                Learn. Create. Collaborate. Share.
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="zoom-in" data-delay="100">
                    <h4 class="text-2xl font-black text-brand-yellow mb-2 tracking-wider">LEARN</h4>
                    <p class="text-gray-400 text-sm">Memahami konsep sains dan gizi secara riil.</p>
                </div>
                <div class="zoom-in" data-delay="200">
                    <h4 class="text-2xl font-black text-brand-yellow mb-2 tracking-wider">CREATE</h4>
                    <p class="text-gray-400 text-sm">Menghasilkan produk makanan dan karya nyata.</p>
                </div>
                <div class="zoom-in" data-delay="300">
                    <h4 class="text-2xl font-black text-brand-yellow mb-2 tracking-wider">COLLABORATE</h4>
                    <p class="text-gray-400 text-sm">Bekerja, diskusi, dan tumbuh dalam tim.</p>
                </div>
                <div class="zoom-in" data-delay="400">
                    <h4 class="text-2xl font-black text-brand-yellow mb-2 tracking-wider">SHARE</h4>
                    <p class="text-gray-400 text-sm">Berbagi ilmu & inspirasi melalui presentasi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 10. FAQ -->
    <section id="faq" class="py-24 bg-white relative">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-up">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-teal-light text-brand-teal font-bold text-xs uppercase tracking-wider mb-3">
                    Punya Pertanyaan?
                </div>
                <h2 class="text-fluid-h2 font-extrabold text-brand-dark">Frequently Asked Questions</h2>
            </div>

            <div class="space-y-4">
                <!-- Q1 -->
                <details class="group bg-brand-light rounded-2xl border border-gray-100 transition-all fade-up" data-delay="100">
                    <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-brand-dark select-none">
                        <span class="text-base sm:text-lg">Siapa yang dapat mengikuti program ini?</span>
                        <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0 ml-2">
                            <i class="ph-bold ph-caret-down text-brand-teal caret-icon text-lg"></i>
                        </span>
                    </summary>
                    <div class="text-gray-600 px-6 pb-6 pt-0 text-sm sm:text-base leading-relaxed">
                        Program ini ditujukan khusus untuk siswa-siswi yang terdaftar dalam kelas IPA atau sesuai dengan target kelas yang ditentukan oleh sekolah (misal: Kelas 8).
                    </div>
                </details>

                <!-- Q2 -->
                <details class="group bg-brand-light rounded-2xl border border-gray-100 transition-all fade-up" data-delay="200">
                    <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-brand-dark select-none">
                        <span class="text-base sm:text-lg">Apakah peserta harus pintar masak atau punya pengalaman sebelumnya?</span>
                        <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0 ml-2">
                            <i class="ph-bold ph-caret-down text-brand-teal caret-icon text-lg"></i>
                        </span>
                    </summary>
                    <div class="text-gray-600 px-6 pb-6 pt-0 text-sm sm:text-base leading-relaxed">
                        Tidak. Program ini dirancang sebagai pengalaman belajar berbasis proyek. Fokus utama adalah pemahaman sains di balik makanan, kreativitas, dan kerja sama tim. Makanan bisa sangat sederhana namun memenuhi syarat gizi.
                    </div>
                </details>

                <!-- Q3 -->
                <details class="group bg-brand-light rounded-2xl border border-gray-100 transition-all fade-up" data-delay="300">
                    <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-brand-dark select-none">
                        <span class="text-base sm:text-lg">Apa saja yang perlu dipersiapkan oleh peserta?</span>
                        <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0 ml-2">
                            <i class="ph-bold ph-caret-down text-brand-teal caret-icon text-lg"></i>
                        </span>
                    </summary>
                    <div class="text-gray-600 px-6 pb-6 pt-0 text-sm sm:text-base leading-relaxed">
                        Peserta perlu menyiapkan konsep menu makanan bergizi, pembagian tugas kelompok, bahan-bahan dasar pembuatan menu, serta materi desain poster untuk dipresentasikan saat acara puncak (Sesuai juknis guru).
                    </div>
                </details>

                <!-- Q4 -->
                <details class="group bg-brand-light rounded-2xl border border-gray-100 transition-all fade-up" data-delay="400">
                    <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-brand-dark select-none">
                        <span class="text-base sm:text-lg">Apakah kegiatan ini diadakan setiap tahun?</span>
                        <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0 ml-2">
                            <i class="ph-bold ph-caret-down text-brand-teal caret-icon text-lg"></i>
                        </span>
                    </summary>
                    <div class="text-gray-600 px-6 pb-6 pt-0 text-sm sm:text-base leading-relaxed">
                        Ya, Science Food Festival dikembangkan sebagai program pameran tahunan untuk memberikan sarana proyek apresiatif yang berkelanjutan bagi siswa angkatan berikutnya.
                    </div>
                </details>
            </div>
        </div>
    </section>

    <!-- 11. FINAL CTA -->
    <section id="register" class="py-24 bg-brand-yellow relative overflow-hidden">
        <div class="parallax-bg absolute top-0 right-0 w-80 h-80 bg-white opacity-25 rounded-full blur-[60px] pointer-events-none" data-speed="0.15"></div>
        <div class="parallax-bg absolute bottom-0 left-0 w-96 h-96 bg-brand-teal opacity-15 rounded-full blur-[70px] pointer-events-none" data-speed="-0.1"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-fluid-h2 font-black text-brand-dark mb-6 leading-tight fade-up">
                Ready to Experience Science Differently?
            </h2>
            <p class="text-lg sm:text-xl text-brand-dark/85 mb-10 max-w-2xl mx-auto leading-relaxed fade-up" data-delay="100">
                Jadikan makanan bukan hanya sesuatu yang kita konsumsi, tetapi juga sesuatu yang dapat kita pelajari secara bermakna.
            </p>
            <div class="fade-up" data-delay="200">
                <a href="https://linkfly.to/ruangbelajar" target="_blank" rel="noopener noreferrer" class="btn-cta-primary inline-flex items-center gap-3 bg-brand-dark hover:bg-black text-white font-extrabold text-lg sm:text-xl px-10 py-5 rounded-full shadow-2xl group">
                    DAFTAR SEKARANG <i class="ph-bold ph-arrow-right group-hover:translate-x-1.5 transition-transform"></i>
                </a>
            </div>
            <p class="mt-8 font-bold text-brand-dark/70 tracking-widest uppercase text-xs sm:text-sm fade-up" data-delay="300">
                Science Food Festival — Learn Science. Create Experience.
            </p>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-brand-teal rounded-xl flex items-center justify-center text-white shadow-sm shrink-0">
                    <i class="ph-bold ph-flask text-lg"></i>
                </div>
                <div class="flex flex-col justify-center">
                    <span class="font-black text-base tracking-tight text-brand-teal leading-none">Science<span class="text-brand-yellow">.</span></span>
                    <span class="font-extrabold text-[11px] tracking-wider text-brand-dark uppercase mt-0.5 leading-none">Food Festival</span>
                </div>
            </div>
            
            <div class="text-gray-500 text-sm font-medium">
                &copy; <span id="year"></span> Science Food Festival. All rights reserved.
            </div>

            <div class="flex gap-3">
                <a href="#" aria-label="Instagram" class="w-10 h-10 rounded-full bg-brand-light flex items-center justify-center text-gray-500 hover:text-brand-teal hover:bg-brand-teal-light transition-colors">
                    <i class="ph-fill ph-instagram-logo text-xl"></i>
                </a>
                <a href="#" aria-label="YouTube" class="w-10 h-10 rounded-full bg-brand-light flex items-center justify-center text-gray-500 hover:text-brand-teal hover:bg-brand-teal-light transition-colors">
                    <i class="ph-fill ph-youtube-logo text-xl"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/academic-year.js') }}"></script>
    <script src="{{ asset('js/gallery.js') }}"></script>
    <script src="{{ asset('js/motion.js') }}"></script>
    <script>
        // Set dynamic copyright year in footer
        document.getElementById('year').textContent = new Date().getFullYear();

        // Mobile Menu Drawer Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Auto close mobile menu when clicking nav item
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    </script>
</body>
</html>
