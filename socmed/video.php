<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Slideshow - Auto Pan & Zoom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
        }
        
        .upload-area {
            border: 2px dashed #4a5568;
            transition: all 0.3s ease;
        }
        
        .upload-area:hover {
            border-color: #667eea;
            background-color: rgba(102, 126, 234, 0.1);
        }
        
        .upload-area.dragover {
            border-color: #667eea;
            background-color: rgba(102, 126, 234, 0.2);
        }
        
        .slideshow-container {
            position: relative;
            overflow: hidden;
            background: #000;
        }
        
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }
        
        .slide.active {
            opacity: 1;
        }
        
        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform-origin: center;
        }
        
        /* Ken Burns Effect Variations with Enhanced Animations */
        .ken-burns-1 img {
            animation: kenBurns1 12s ease-in-out infinite;
        }
        
        .ken-burns-2 img {
            animation: kenBurns2 14s ease-in-out infinite;
        }
        
        .ken-burns-3 img {
            animation: kenBurns3 16s ease-in-out infinite;
        }
        
        .ken-burns-4 img {
            animation: kenBurns4 13s ease-in-out infinite;
        }
        
        .ken-burns-5 img {
            animation: kenBurns5 15s ease-in-out infinite;
        }
        
        @keyframes kenBurns1 {
            0% { transform: scale(1) translate(0, 0) rotate(0deg); filter: brightness(1) contrast(1); }
            25% { transform: scale(1.08) translate(-1%, -0.5%) rotate(0.2deg); filter: brightness(1.05) contrast(1.02); }
            50% { transform: scale(1.15) translate(-2%, -1%) rotate(0deg); filter: brightness(1.1) contrast(1.05); }
            75% { transform: scale(1.22) translate(-3%, -1.5%) rotate(-0.1deg); filter: brightness(1.08) contrast(1.03); }
            100% { transform: scale(1.3) translate(-4%, -2%) rotate(0deg); filter: brightness(1) contrast(1); }
        }
        
        @keyframes kenBurns2 {
            0% { transform: scale(1.3) translate(4%, 2%) rotate(0deg); filter: brightness(1) contrast(1); }
            25% { transform: scale(1.22) translate(3%, 1.5%) rotate(-0.15deg); filter: brightness(1.03) contrast(1.02); }
            50% { transform: scale(1.15) translate(2%, 1%) rotate(0deg); filter: brightness(1.08) contrast(1.05); }
            75% { transform: scale(1.08) translate(1%, 0.5%) rotate(0.1deg); filter: brightness(1.05) contrast(1.03); }
            100% { transform: scale(1) translate(0, 0) rotate(0deg); filter: brightness(1) contrast(1); }
        }
        
        @keyframes kenBurns3 {
            0% { transform: scale(1) translate(0, 0) rotate(0deg); filter: brightness(1) contrast(1) saturate(1); }
            20% { transform: scale(1.05) translate(1%, -0.5%) rotate(0.1deg); filter: brightness(1.02) contrast(1.01) saturate(1.05); }
            40% { transform: scale(1.12) translate(2%, -1%) rotate(0deg); filter: brightness(1.06) contrast(1.03) saturate(1.1); }
            60% { transform: scale(1.2) translate(3%, -2%) rotate(-0.05deg); filter: brightness(1.08) contrast(1.05) saturate(1.08); }
            80% { transform: scale(1.3) translate(5%, -3%) rotate(0.05deg); filter: brightness(1.04) contrast(1.02) saturate(1.03); }
            100% { transform: scale(1.4) translate(6%, -4%) rotate(0deg); filter: brightness(1) contrast(1) saturate(1); }
        }
        
        @keyframes kenBurns4 {
            0% { transform: scale(1.2) translate(-3%, 3%) rotate(0deg); filter: brightness(1) contrast(1); }
            30% { transform: scale(1.15) translate(-2.5%, 2.5%) rotate(0.08deg); filter: brightness(1.04) contrast(1.02); }
            60% { transform: scale(1.1) translate(-1.5%, 1.5%) rotate(0deg); filter: brightness(1.07) contrast(1.04); }
            90% { transform: scale(1.05) translate(-0.5%, 0.5%) rotate(-0.05deg); filter: brightness(1.03) contrast(1.01); }
            100% { transform: scale(1) translate(0, 0) rotate(0deg); filter: brightness(1) contrast(1); }
        }
        
        @keyframes kenBurns5 {
            0% { transform: scale(1) translate(0, 0) rotate(0deg); filter: brightness(1) contrast(1) hue-rotate(0deg); }
            25% { transform: scale(1.1) translate(-0.5%, 1.5%) rotate(0.1deg); filter: brightness(1.03) contrast(1.02) hue-rotate(2deg); }
            50% { transform: scale(1.25) translate(-1%, 3%) rotate(0deg); filter: brightness(1.08) contrast(1.05) hue-rotate(5deg); }
            75% { transform: scale(1.4) translate(-1.5%, 4.5%) rotate(-0.08deg); filter: brightness(1.05) contrast(1.03) hue-rotate(3deg); }
            100% { transform: scale(1.5) translate(-2%, 6%) rotate(0deg); filter: brightness(1) contrast(1) hue-rotate(0deg); }
        }
        
        /* Particle System */
        .particles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 2;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
            border-radius: 50%;
            pointer-events: none;
            animation: floatParticle linear infinite;
        }
        
        .particle.type-1 {
            width: 3px;
            height: 3px;
            animation-duration: 8s;
            background: radial-gradient(circle, rgba(102,126,234,0.7) 0%, rgba(102,126,234,0.3) 50%, transparent 100%);
        }
        
        .particle.type-2 {
            width: 2px;
            height: 2px;
            animation-duration: 12s;
            background: radial-gradient(circle, rgba(255,255,255,0.6) 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
        }
        
        .particle.type-3 {
            width: 4px;
            height: 4px;
            animation-duration: 15s;
            background: radial-gradient(circle, rgba(240,147,251,0.5) 0%, rgba(240,147,251,0.2) 50%, transparent 100%);
        }
        
        .particle.type-4 {
            width: 1px;
            height: 1px;
            animation-duration: 6s;
            background: radial-gradient(circle, rgba(255,215,0,0.8) 0%, rgba(255,215,0,0.4) 50%, transparent 100%);
        }
        
        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) translateX(0) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: translateY(90vh) translateX(10px) scale(1);
            }
            50% {
                opacity: 0.8;
                transform: translateY(50vh) translateX(-20px) scale(1.2);
            }
            90% {
                opacity: 0.3;
                transform: translateY(10vh) translateX(15px) scale(0.8);
            }
            100% {
                transform: translateY(-10vh) translateX(0) scale(0);
                opacity: 0;
            }
        }
        
        /* Dynamic Light Effects */
        .light-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 3;
            background: linear-gradient(45deg, 
                rgba(102,126,234,0.1) 0%, 
                transparent 25%, 
                rgba(240,147,251,0.08) 50%, 
                transparent 75%, 
                rgba(67,233,123,0.06) 100%);
            animation: lightSweep 20s ease-in-out infinite;
        }
        
        @keyframes lightSweep {
            0% {
                background: linear-gradient(45deg, 
                    rgba(102,126,234,0.1) 0%, 
                    transparent 25%, 
                    rgba(240,147,251,0.08) 50%, 
                    transparent 75%, 
                    rgba(67,233,123,0.06) 100%);
                transform: translateX(-100%);
            }
            25% {
                background: linear-gradient(135deg, 
                    rgba(240,147,251,0.12) 0%, 
                    transparent 30%, 
                    rgba(79,172,254,0.1) 60%, 
                    transparent 80%, 
                    rgba(255,215,0,0.08) 100%);
                transform: translateX(0%);
            }
            50% {
                background: linear-gradient(225deg, 
                    rgba(67,233,123,0.1) 0%, 
                    transparent 20%, 
                    rgba(247,112,98,0.08) 40%, 
                    transparent 70%, 
                    rgba(102,126,234,0.06) 100%);
                transform: translateX(50%);
            }
            75% {
                background: linear-gradient(315deg, 
                    rgba(255,215,0,0.12) 0%, 
                    transparent 35%, 
                    rgba(240,147,251,0.09) 65%, 
                    transparent 85%, 
                    rgba(79,172,254,0.07) 100%);
                transform: translateX(25%);
            }
            100% {
                background: linear-gradient(45deg, 
                    rgba(102,126,234,0.1) 0%, 
                    transparent 25%, 
                    rgba(240,147,251,0.08) 50%, 
                    transparent 75%, 
                    rgba(67,233,123,0.06) 100%);
                transform: translateX(-100%);
            }
        }
        
        /* Bokeh Effects */
        .bokeh-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }
        
        .bokeh {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 40%, transparent 70%);
            animation: bokehFloat linear infinite;
            filter: blur(1px);
        }
        
        .bokeh.size-1 {
            width: 20px;
            height: 20px;
            animation-duration: 25s;
        }
        
        .bokeh.size-2 {
            width: 35px;
            height: 35px;
            animation-duration: 30s;
        }
        
        .bokeh.size-3 {
            width: 15px;
            height: 15px;
            animation-duration: 20s;
        }
        
        @keyframes bokehFloat {
            0% {
                transform: translateY(100vh) translateX(0) scale(0);
                opacity: 0;
            }
            5% {
                opacity: 0.6;
                transform: translateY(95vh) translateX(20px) scale(1);
            }
            50% {
                opacity: 0.3;
                transform: translateY(50vh) translateX(-30px) scale(1.1);
            }
            95% {
                opacity: 0.1;
                transform: translateY(5vh) translateX(10px) scale(0.9);
            }
            100% {
                transform: translateY(-5vh) translateX(0) scale(0);
                opacity: 0;
            }
        }
        
        /* Enhanced Slide Transitions */
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: all 2s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(1.05);
        }
        
        .slide.active {
            opacity: 1;
            transform: scale(1);
        }
        
        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(0,0,0,0.1) 0%, 
                transparent 30%, 
                rgba(255,255,255,0.05) 70%, 
                rgba(0,0,0,0.1) 100%);
            z-index: 1;
            pointer-events: none;
            animation: overlayShimmer 8s ease-in-out infinite;
        }
        
        @keyframes overlayShimmer {
            0%, 100% {
                background: linear-gradient(135deg, 
                    rgba(0,0,0,0.1) 0%, 
                    transparent 30%, 
                    rgba(255,255,255,0.05) 70%, 
                    rgba(0,0,0,0.1) 100%);
            }
            50% {
                background: linear-gradient(225deg, 
                    rgba(0,0,0,0.05) 0%, 
                    rgba(255,255,255,0.08) 20%, 
                    transparent 50%, 
                    rgba(255,255,255,0.03) 80%, 
                    rgba(0,0,0,0.08) 100%);
            }
        }
        
        /* Text Overlay Styles */
        .text-overlay {
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 5;
            opacity: 1;
            pointer-events: none;
            width: 90%;
        }
        
        .main-title {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4rem;
            font-weight: 900;
            color: #ffffff;
            text-shadow: 
                0 0 20px rgba(0, 0, 0, 0.9),
                0 0 40px rgba(0, 0, 0, 0.7),
                3px 3px 6px rgba(0, 0, 0, 1);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 3px;
            width: 90%;
            text-align: center;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            z-index: 10;
        }
        
        .main-title.show {
            opacity: 1;
        }
        
        .main-title.gradient-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .main-title.gradient-pink {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .main-title.gradient-blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .main-title.gradient-green {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .main-title.gradient-sunset {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .main-title.white {
            color: #ffffff;
            -webkit-text-fill-color: #ffffff;
            background: none;
        }
        
        .main-title.gold {
            color: #ffd700;
            -webkit-text-fill-color: #ffd700;
            background: none;
        }
        
        .main-title.red {
            color: #ff6b6b;
            -webkit-text-fill-color: #ff6b6b;
            background: none;
        }
        
        .caption-text {
            font-size: 1.8rem;
            font-weight: 600;
            color: #ffffff;
            text-shadow: 
                2px 2px 4px rgba(0, 0, 0, 1),
                -2px -2px 4px rgba(0, 0, 0, 1),
                2px -2px 4px rgba(0, 0, 0, 1),
                -2px 2px 4px rgba(0, 0, 0, 1);
            background: rgba(0, 0, 0, 0.8);
            padding: 12px 24px;
            border-radius: 8px;
            line-height: 1.5;
            max-width: 90%;
            word-wrap: break-word;
        }
        

        
        .thumbnail-strip {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: thin;
            scrollbar-color: #667eea #2d3748;
        }
        
        .thumbnail {
            flex-shrink: 0;
            width: 80px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .thumbnail:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        
        .thumbnail.active {
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
        }
        
        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        @keyframes fadeInCenter {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }
        
        .main-title.animate {
            animation: fadeInCenter 1s ease-out;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Professional Slideshow</h1>
        </div>

        <!-- Upload Area -->
        <div id="uploadSection" class="mb-6">
            <div id="uploadArea" class="upload-area rounded-xl p-8 text-center bg-gray-800">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="text-lg font-medium text-gray-300 mb-2">Upload Multiple Images</p>
                <p class="text-sm text-gray-500 mb-4">Drag & drop atau pilih beberapa gambar sekaligus</p>
                <input type="file" id="fileInput" accept="image/*" multiple class="hidden">
                <button id="uploadBtn" class="bg-indigo-600 hover:bg-indigo-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Pilih Gambar
                </button>
            </div>
        </div>

        <!-- Slideshow Container -->
        <div id="slideshowSection" class="hidden">
            <div id="slideshowContainer" class="slideshow-container overflow-hidden mb-4" style="width: 1280px; height: 720px; max-width: 100%;">
                <div id="slidesContainer"></div>
                
                <!-- Bokeh Effects -->
                <div id="bokehContainer" class="bokeh-container"></div>
                
                <!-- Particle System -->
                <div id="particlesContainer" class="particles-container"></div>
                
                <!-- Dynamic Light Overlay -->
                <div class="light-overlay"></div>
                
                <!-- Text Overlay -->
                <div id="textOverlay" class="text-overlay">
                    <div id="mainTitle" class="main-title"></div>
                    <div id="captionText" class="caption-text"></div>
                </div>

            </div>
            
            <!-- Settings -->
            <div class="bg-gray-800 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between">
                    <span class="font-medium">Kecepatan Slide:</span>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-400">Lambat</span>
                        <input type="range" id="speedSlider" min="3" max="12" value="3" class="w-32">
                        <span class="text-sm text-gray-400">Cepat</span>
                        <span id="speedValue" class="text-sm font-medium ml-2">3s</span>
                    </div>
                </div>
            </div>
            
            <!-- Thumbnails -->
            <div class="bg-gray-800 rounded-xl p-4 mb-4">
                <h3 class="font-medium mb-3">Gambar (<span id="imageCount">0</span>)</h3>
                <div class="flex items-center gap-3">
                    <div id="thumbnailStrip" class="thumbnail-strip flex-1"></div>
                    <button id="addMoreBtn" class="flex-shrink-0 w-16 h-12 bg-indigo-600 hover:bg-indigo-700 rounded-lg flex items-center justify-center transition-colors" title="Tambah gambar lain">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
                <input type="file" id="addMoreInput" accept="image/*" multiple class="hidden">
            </div>
            
            <!-- Settings Form -->
            <div class="bg-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Pengaturan Teks</h3>
                
                <div class="space-y-4">
                    <!-- Main Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Judul Utama</label>
                        <input type="text" id="mainTitleInput" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:outline-none" placeholder="Masukkan judul utama...">
                        
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Warna Judul</label>
                            <div class="flex gap-2 flex-wrap">
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" data-color="gradient-purple" title="Gradient Ungu"></button>
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);" data-color="gradient-pink" title="Gradient Pink"></button>
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);" data-color="gradient-blue" title="Gradient Biru"></button>
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);" data-color="gradient-green" title="Gradient Hijau"></button>
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);" data-color="gradient-sunset" title="Gradient Sunset"></button>
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: #ffffff;" data-color="white" title="Putih"></button>
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: #ffd700;" data-color="gold" title="Emas"></button>
                                <button type="button" class="title-color-btn w-8 h-8 rounded-full border-2 border-gray-600" style="background: #ff6b6b;" data-color="red" title="Merah"></button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Captions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Caption 1</label>
                        <textarea id="caption1" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:outline-none resize-none" placeholder="Caption pertama..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Caption 2</label>
                        <textarea id="caption2" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:outline-none resize-none" placeholder="Caption kedua..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Caption 3</label>
                        <textarea id="caption3" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:outline-none resize-none" placeholder="Caption ketiga..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Caption 4</label>
                        <textarea id="caption4" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:outline-none resize-none" placeholder="Caption keempat..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Caption 5</label>
                        <textarea id="caption5" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:outline-none resize-none" placeholder="Caption kelima..."></textarea>
                    </div>
                    
                    <!-- Toggles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Mode Random</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="randomMode" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Tampilkan Judul (30 detik)</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="showTitleToggle" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <button id="saveSettings" class="w-full bg-indigo-600 hover:bg-indigo-700 px-4 py-3 rounded-lg font-medium transition-colors mt-4">
                        Simpan Pengaturan
                    </button>
                    
                    <!-- Reset Button -->
                    <button id="resetBtn" class="w-full bg-gray-700 hover:bg-gray-600 px-4 py-3 rounded-lg font-medium transition-colors">
                        Reset Slideshow
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>
        let images = [];
        let currentSlide = 0;
        let isPlaying = true;
        let slideInterval;
        let progressInterval;
        let captionInterval;
        let slideDuration = 3000; // 3 seconds default
        let isRandomMode = true;
        let slideOrder = [];
        let currentSlideIndex = 0;
        
        // Text settings
        let mainTitle = '';
        let captions = ['', '', '', '', ''];
        let currentCaptionIndex = 0;
        let showMainTitle = true;
        let titleInterval;
        let titleColor = 'gradient-purple';
        
        // Ken Burns effects array
        const kenBurnsEffects = ['ken-burns-1', 'ken-burns-2', 'ken-burns-3', 'ken-burns-4', 'ken-burns-5'];

        // Elements
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        const uploadSection = document.getElementById('uploadSection');
        const slideshowSection = document.getElementById('slideshowSection');
        const slidesContainer = document.getElementById('slidesContainer');
        const thumbnailStrip = document.getElementById('thumbnailStrip');
        const imageCount = document.getElementById('imageCount');
        const speedSlider = document.getElementById('speedSlider');
        const speedValue = document.getElementById('speedValue');
        const resetBtn = document.getElementById('resetBtn');
        const addMoreBtn = document.getElementById('addMoreBtn');
        const addMoreInput = document.getElementById('addMoreInput');
        
        // Text overlay elements
        const textOverlay = document.getElementById('textOverlay');
        const mainTitleElement = document.getElementById('mainTitle');
        const captionTextElement = document.getElementById('captionText');
        
        // Effect containers
        const particlesContainer = document.getElementById('particlesContainer');
        const bokehContainer = document.getElementById('bokehContainer');
        
        // Settings form elements
        const mainTitleInput = document.getElementById('mainTitleInput');
        const caption1 = document.getElementById('caption1');
        const caption2 = document.getElementById('caption2');
        const caption3 = document.getElementById('caption3');
        const caption4 = document.getElementById('caption4');
        const caption5 = document.getElementById('caption5');
        const randomMode = document.getElementById('randomMode');
        const showTitleToggle = document.getElementById('showTitleToggle');
        const saveSettings = document.getElementById('saveSettings');
        


        // Upload functionality
        uploadBtn.addEventListener('click', () => fileInput.click());
        addMoreBtn.addEventListener('click', () => addMoreInput.click());
        
        fileInput.addEventListener('change', handleFileSelect);
        addMoreInput.addEventListener('change', handleAddMoreFiles);
        
        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });

        function handleFileSelect(e) {
            const files = Array.from(e.target.files);
            handleFiles(files);
        }

        function handleFiles(files) {
            const imageFiles = files.filter(file => file.type.startsWith('image/'));
            
            if (imageFiles.length === 0) {
                alert('Mohon pilih file gambar yang valid');
                return;
            }

            images = [];
            let loadedCount = 0;

            imageFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    images.push({
                        src: e.target.result,
                        name: file.name
                    });
                    loadedCount++;
                    
                    if (loadedCount === imageFiles.length) {
                        initializeSlideshow();
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        function handleAddMoreFiles(e) {
            const files = Array.from(e.target.files);
            addMoreImages(files);
        }

        function addMoreImages(files) {
            const imageFiles = files.filter(file => file.type.startsWith('image/'));
            
            if (imageFiles.length === 0) {
                alert('Mohon pilih file gambar yang valid');
                return;
            }

            const startIndex = images.length;
            let loadedCount = 0;

            imageFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    images.push({
                        src: e.target.result,
                        name: file.name
                    });
                    loadedCount++;
                    
                    if (loadedCount === imageFiles.length) {
                        // Update slideshow with new images
                        updateSlideshowWithNewImages(startIndex);
                        // Clear the input
                        addMoreInput.value = '';
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        function updateSlideshowWithNewImages(startIndex) {
            // Stop current slideshow
            const wasPlaying = isPlaying;
            if (isPlaying) {
                stopSlideshow();
            }
            
            // Add new slides
            for (let i = startIndex; i < images.length; i++) {
                const image = images[i];
                const slide = document.createElement('div');
                slide.className = `slide ${kenBurnsEffects[i % kenBurnsEffects.length]}`;
                
                const img = document.createElement('img');
                img.src = image.src;
                img.alt = image.name;
                
                slide.appendChild(img);
                slidesContainer.appendChild(slide);
                
                // Add thumbnail
                const thumbnail = document.createElement('div');
                thumbnail.className = 'thumbnail';
                
                const thumbImg = document.createElement('img');
                thumbImg.src = image.src;
                thumbImg.alt = image.name;
                
                thumbnail.appendChild(thumbImg);
                thumbnail.addEventListener('click', () => goToSlide(i));
                thumbnailStrip.appendChild(thumbnail);
            }
            
            // Update counter and regenerate slide order
            updateCounter();
            generateSlideOrder();
            
            // Restart slideshow if it was playing
            if (wasPlaying) {
                startSlideshow();
            }
        }

        // Particle System Functions
        function createParticle() {
            const particle = document.createElement('div');
            const types = ['type-1', 'type-2', 'type-3', 'type-4'];
            const randomType = types[Math.floor(Math.random() * types.length)];
            
            particle.className = `particle ${randomType}`;
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 2 + 's';
            
            particlesContainer.appendChild(particle);
            
            // Remove particle after animation
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 15000);
        }
        
        function createBokeh() {
            const bokeh = document.createElement('div');
            const sizes = ['size-1', 'size-2', 'size-3'];
            const randomSize = sizes[Math.floor(Math.random() * sizes.length)];
            
            bokeh.className = `bokeh ${randomSize}`;
            bokeh.style.left = Math.random() * 100 + '%';
            bokeh.style.animationDelay = Math.random() * 5 + 's';
            
            // Random colors for bokeh
            const colors = [
                'rgba(102,126,234,0.3)',
                'rgba(240,147,251,0.25)',
                'rgba(67,233,123,0.2)',
                'rgba(255,215,0,0.3)',
                'rgba(255,255,255,0.2)'
            ];
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            bokeh.style.background = `radial-gradient(circle, ${randomColor} 0%, transparent 70%)`;
            
            bokehContainer.appendChild(bokeh);
            
            // Remove bokeh after animation
            setTimeout(() => {
                if (bokeh.parentNode) {
                    bokeh.parentNode.removeChild(bokeh);
                }
            }, 35000);
        }
        
        function startParticleSystem() {
            // Create particles continuously
            setInterval(() => {
                if (particlesContainer && images.length > 0) {
                    createParticle();
                }
            }, 800);
            
            // Create bokeh effects
            setInterval(() => {
                if (bokehContainer && images.length > 0) {
                    createBokeh();
                }
            }, 2000);
        }
        
        function initializeSlideshow() {
            uploadSection.classList.add('hidden');
            slideshowSection.classList.remove('hidden');
            
            createSlides();
            createThumbnails();
            generateSlideOrder();
            updateCounter();
            
            // Start particle system
            startParticleSystem();
            
            // Show title at start if available
            if (mainTitle && showMainTitle) {
                showText(true);
                
                // Hide title after 5 seconds
                setTimeout(() => {
                    hideText();
                    
                    // Show captions after title disappears
                    if (captions.some(c => c.trim() !== '')) {
                        setTimeout(() => {
                            showText(false);
                        }, 500);
                    }
                }, 5000);
            } else if (captions.some(c => c.trim() !== '')) {
                // Show initial caption if no title
                showText(false);
            }
            
            // Start auto slideshow immediately
            setTimeout(() => {
                startSlideshow();
                startCaptionRotation();
                startTitleTimer();
            }, 100);
        }
        
        function generateSlideOrder() {
            slideOrder = Array.from({length: images.length}, (_, i) => i);
            if (isRandomMode) {
                // Fisher-Yates shuffle
                for (let i = slideOrder.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [slideOrder[i], slideOrder[j]] = [slideOrder[j], slideOrder[i]];
                }
            }
            currentSlideIndex = 0;
        }
        
        function calculateCaptionDuration(text) {
            if (!text) return 3000;
            const charCount = text.length;
            // 3 seconds minimum, up to 10 seconds based on character count
            return Math.min(Math.max(charCount * 50, 3000), 10000);
        }
        
        function showText(showTitle = false) {
            if (mainTitle && showTitle && showMainTitle) {
                mainTitleElement.textContent = mainTitle;
                mainTitleElement.className = `main-title ${titleColor}`;
                mainTitleElement.style.display = 'block';
                mainTitleElement.classList.add('show');
            } else {
                mainTitleElement.style.display = 'none';
                mainTitleElement.classList.remove('show');
            }
            
            const validCaptions = captions.filter(caption => caption.trim() !== '');
            if (validCaptions.length > 0 && !showTitle) {
                const currentCaption = validCaptions[currentCaptionIndex % validCaptions.length];
                captionTextElement.textContent = currentCaption;
                captionTextElement.style.display = 'block';
            } else if (!showTitle) {
                captionTextElement.style.display = 'none';
            }
            
            if (showTitle && mainTitle && showMainTitle) {
                captionTextElement.style.display = 'none';
            }
        }
        
        function hideText() {
            // Only hide title, keep captions visible
            mainTitleElement.style.display = 'none';
            mainTitleElement.classList.remove('show');
        }
        
        function startCaptionRotation() {
            const validCaptions = captions.filter(caption => caption.trim() !== '');
            if (validCaptions.length === 0) return;
            
            const rotateCaptions = () => {
                const currentCaption = validCaptions[currentCaptionIndex % validCaptions.length];
                const duration = calculateCaptionDuration(currentCaption);
                
                captionInterval = setTimeout(() => {
                    currentCaptionIndex++;
                    if (validCaptions.length > 0) {
                        showText(false);
                    }
                    rotateCaptions();
                }, duration);
            };
            
            rotateCaptions();
        }
        
        function startTitleTimer() {
            if (!showMainTitle || !mainTitle) return;
            
            const showTitlePeriodically = () => {
                // Show title for 5 seconds
                showText(true);
                
                setTimeout(() => {
                    hideText();
                    
                    // Wait 25 seconds before showing again (total 30 seconds cycle)
                    titleInterval = setTimeout(() => {
                        showTitlePeriodically();
                    }, 25000);
                }, 5000);
            };
            
            // Start after 30 seconds
            titleInterval = setTimeout(() => {
                showTitlePeriodically();
            }, 30000);
        }

        function createSlides() {
            slidesContainer.innerHTML = '';
            
            images.forEach((image, index) => {
                const slide = document.createElement('div');
                slide.className = `slide ${kenBurnsEffects[index % kenBurnsEffects.length]}`;
                if (index === 0) slide.classList.add('active');
                
                const img = document.createElement('img');
                img.src = image.src;
                img.alt = image.name;
                
                slide.appendChild(img);
                slidesContainer.appendChild(slide);
            });
        }

        function createThumbnails() {
            thumbnailStrip.innerHTML = '';
            imageCount.textContent = images.length;
            
            images.forEach((image, index) => {
                const thumbnail = document.createElement('div');
                thumbnail.className = 'thumbnail';
                if (index === 0) thumbnail.classList.add('active');
                
                const img = document.createElement('img');
                img.src = image.src;
                img.alt = image.name;
                
                thumbnail.appendChild(img);
                thumbnail.addEventListener('click', () => goToSlide(index));
                thumbnailStrip.appendChild(thumbnail);
            });
        }

        function goToSlide(index) {
            if (index === currentSlide) return;
            
            // Remove active class from current slide and thumbnail
            document.querySelectorAll('.slide')[currentSlide].classList.remove('active');
            document.querySelectorAll('.thumbnail')[currentSlide].classList.remove('active');
            
            currentSlide = index;
            
            // Add active class to new slide and thumbnail
            document.querySelectorAll('.slide')[currentSlide].classList.add('active');
            document.querySelectorAll('.thumbnail')[currentSlide].classList.add('active');
            
            updateCounter();
            resetProgress();
            
            // Show caption overlay (not title)
            if (captions.some(c => c.trim() !== '')) {
                showText(false);
            }
        }

        function nextSlide() {
            if (isRandomMode) {
                currentSlideIndex = (currentSlideIndex + 1) % slideOrder.length;
                if (currentSlideIndex === 0) {
                    // Reshuffle when we complete a cycle
                    generateSlideOrder();
                }
                goToSlide(slideOrder[currentSlideIndex]);
            } else {
                const nextIndex = (currentSlide + 1) % images.length;
                goToSlide(nextIndex);
            }
        }

        function prevSlide() {
            if (isRandomMode) {
                currentSlideIndex = (currentSlideIndex - 1 + slideOrder.length) % slideOrder.length;
                goToSlide(slideOrder[currentSlideIndex]);
            } else {
                const prevIndex = (currentSlide - 1 + images.length) % images.length;
                goToSlide(prevIndex);
            }
        }

        function startSlideshow() {
            if (images.length <= 1) return;
            
            slideInterval = setInterval(nextSlide, slideDuration);
            startProgress();
            isPlaying = true;
            if (playPauseBtn) playPauseBtn.textContent = '⏸';
        }

        function stopSlideshow() {
            clearInterval(slideInterval);
            clearInterval(progressInterval);
            clearTimeout(captionInterval);
            clearTimeout(titleInterval);
            isPlaying = false;
            if (playPauseBtn) playPauseBtn.textContent = '▶';
        }

        function startProgress() {
            let progress = 0;
            const increment = 100 / (slideDuration / 100);
            
            progressInterval = setInterval(() => {
                progress += increment;
                if (progressBar) progressBar.style.width = progress + '%';
                
                if (progress >= 100) {
                    progress = 0;
                }
            }, 100);
        }

        function resetProgress() {
            clearInterval(progressInterval);
            if (progressBar) progressBar.style.width = '0%';
            if (isPlaying) startProgress();
        }

        function updateCounter() {
            if (slideCounter) slideCounter.textContent = `${currentSlide + 1} / ${images.length}`;
        }

        // Event listeners
        speedSlider.addEventListener('input', (e) => {
            slideDuration = parseInt(e.target.value) * 1000;
            speedValue.textContent = e.target.value + 's';
            
            if (isPlaying) {
                stopSlideshow();
                startSlideshow();
            }
        });

        resetBtn.addEventListener('click', () => {
            stopSlideshow();
            images = [];
            currentSlide = 0;
            fileInput.value = '';
            uploadSection.classList.remove('hidden');
            slideshowSection.classList.add('hidden');
            slidesContainer.innerHTML = '';
            thumbnailStrip.innerHTML = '';
            hideText();
            
            // Clear particle effects
            if (particlesContainer) particlesContainer.innerHTML = '';
            if (bokehContainer) bokehContainer.innerHTML = '';
            
            // Reset text settings
            mainTitle = '';
            captions = ['', '', '', '', ''];
            currentCaptionIndex = 0;
            showMainTitle = true;
            
            // Clear form inputs
            if (mainTitleInput) mainTitleInput.value = '';
            if (caption1) caption1.value = '';
            if (caption2) caption2.value = '';
            if (caption3) caption3.value = '';
            if (caption4) caption4.value = '';
            if (caption5) caption5.value = '';
            if (randomMode) randomMode.checked = true;
            if (showTitleToggle) showTitleToggle.checked = true;
        });

        // Color selection event listeners
        document.addEventListener('DOMContentLoaded', () => {
            // Load saved settings first
            loadSettings();
            
            const colorButtons = document.querySelectorAll('.title-color-btn');
            
            // Set initial active color
            colorButtons.forEach(btn => {
                if (btn.dataset.color === titleColor) {
                    btn.classList.add('ring-2', 'ring-indigo-500');
                }
                
                btn.addEventListener('click', () => {
                    // Remove active state from all buttons
                    colorButtons.forEach(b => b.classList.remove('ring-2', 'ring-indigo-500'));
                    
                    // Add active state to clicked button
                    btn.classList.add('ring-2', 'ring-indigo-500');
                    
                    // Update title color
                    titleColor = btn.dataset.color;
                    
                    // Update title display if visible
                    if (mainTitleElement.style.display !== 'none') {
                        mainTitleElement.className = `main-title ${titleColor}`;
                        if (mainTitleElement.classList.contains('show')) {
                            mainTitleElement.classList.add('show');
                        }
                    }
                });
            });
        });

        // Load settings from localStorage on page load
        function loadSettings() {
            const savedSettings = localStorage.getItem('slideshowSettings');
            if (savedSettings) {
                const settings = JSON.parse(savedSettings);
                
                mainTitle = settings.mainTitle || '';
                captions = settings.captions || ['', '', '', '', ''];
                isRandomMode = settings.isRandomMode !== undefined ? settings.isRandomMode : true;
                showMainTitle = settings.showMainTitle !== undefined ? settings.showMainTitle : true;
                titleColor = settings.titleColor || 'gradient-purple';
                
                // Update form inputs
                if (mainTitleInput) mainTitleInput.value = mainTitle;
                if (caption1) caption1.value = captions[0];
                if (caption2) caption2.value = captions[1];
                if (caption3) caption3.value = captions[2];
                if (caption4) caption4.value = captions[3];
                if (caption5) caption5.value = captions[4];
                if (randomMode) randomMode.checked = isRandomMode;
                if (showTitleToggle) showTitleToggle.checked = showMainTitle;
                
                // Update color selection
                document.querySelectorAll('.title-color-btn').forEach(btn => {
                    btn.classList.remove('ring-2', 'ring-indigo-500');
                    if (btn.dataset.color === titleColor) {
                        btn.classList.add('ring-2', 'ring-indigo-500');
                    }
                });
            }
        }

        // Save settings to localStorage
        function saveSettingsToLocal() {
            const settings = {
                mainTitle: mainTitle,
                captions: captions,
                isRandomMode: isRandomMode,
                showMainTitle: showMainTitle,
                titleColor: titleColor
            };
            localStorage.setItem('slideshowSettings', JSON.stringify(settings));
        }

        // Settings form event listener
        saveSettings.addEventListener('click', () => {
            // Save settings
            mainTitle = mainTitleInput.value;
            captions[0] = caption1.value;
            captions[1] = caption2.value;
            captions[2] = caption3.value;
            captions[3] = caption4.value;
            captions[4] = caption5.value;
            isRandomMode = randomMode.checked;
            showMainTitle = showTitleToggle.checked;
            
            // Save to localStorage
            saveSettingsToLocal();
            
            // Regenerate slide order if random mode changed
            if (images.length > 0) {
                generateSlideOrder();
                
                // Restart caption rotation
                clearTimeout(captionInterval);
                clearTimeout(titleInterval);
                currentCaptionIndex = 0;
                startCaptionRotation();
                startTitleTimer();
                
                // Show updated caption text (not title)
                if (captions.some(c => c.trim() !== '')) {
                    showText(false);
                } else {
                    hideText();
                }
            }
        });

        // Keyboard controls
        document.addEventListener('keydown', (e) => {
            // Don't interfere with typing in input fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }
            
            if (images.length === 0) return;
            
            switch(e.key) {
                case 'ArrowLeft':
                    prevSlide();
                    if (isPlaying) {
                        stopSlideshow();
                        startSlideshow();
                    }
                    break;
                case 'ArrowRight':
                    nextSlide();
                    if (isPlaying) {
                        stopSlideshow();
                        startSlideshow();
                    }
                    break;
                case ' ':
                    e.preventDefault();
                    if (isPlaying) {
                        stopSlideshow();
                    } else {
                        startSlideshow();
                    }
                    break;
            }
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'988463b1a06681c2',t:'MTc1OTQxMDA5Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
