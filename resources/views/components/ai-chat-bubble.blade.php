<!-- STEMAN AI Assistant Floating Bubble -->
<div id="ai-assistant-wrapper" style="position: fixed; bottom: 85px; right: 20px; z-index: 99999; transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);">
    
    <!-- Ghost Tab (Visible when hidden) -->
    <div id="ai-ghost-tab" class="d-none bg-warning shadow-sm d-flex align-items-center justify-content-center cursor-pointer" 
         onclick="reviveAIBuddy()"
         style="position: fixed; right: -5px; bottom: 100px; width: 35px; height: 60px; border-radius: 20px 0 0 20px; border: 2px solid white; cursor: pointer;">
        <i class="bi bi-robot text-dark fs-5"></i>
    </div>

    <!-- Chat Window (Initial status: Hidden) -->
    <div id="ai-chat-window" class="shadow-lg overflow-hidden d-none animate__animated" style="width: 350px; border-radius: 24px; background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.4); margin-bottom: 20px;">
        <!-- Header -->
        <div class="p-3 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <div class="d-flex align-items-center">
                <div class="bg-warning rounded-circle p-2 me-2 shadow-sm">
                    <i class="bi bi-robot text-dark"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Steman AI Buddy</h6>
                    <small class="opacity-75" style="font-size: 0.7rem;">⚡ Online & Helpful</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white shadow-none" onclick="toggleAIChat()" style="font-size: 0.7rem;"></button>
        </div>

        <!-- Chat Area -->
        <div id="ai-chat-body" class="p-3" style="height: 350px; overflow-y: auto; background: rgba(255,255,255,0.5);">
            <div class="ai-msg mb-4">
                <div class="p-2 px-3 rounded-4 bg-light small shadow-sm d-inline-block" style="max-width: 85%;">
                    Halo Kak! Saya Steman AI. Ada yang bisa saya bantu seputar portal atau jaringan alumni SMKN 2? 👋
                </div>
                <div class="text-muted mt-1" style="font-size: 0.6rem;">Just now</div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 border-top bg-white bg-opacity-50">
            <div class="input-group">
                <input type="text" id="ai-chat-input" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 shadow-none" placeholder="Tanya sesuatu..." onkeypress="handleChatEnter(event)">
                <button class="btn btn-warning btn-sm rounded-circle ms-2" onclick="sendAIChat()" style="width: 32px; height: 32px;">
                    <i class="bi bi-send-fill text-dark" style="font-size: 0.8rem;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Trigger Button -->
    <div id="ai-trigger-group" class="position-relative">
        <button id="ai-chat-dismiss" class="btn btn-danger btn-sm rounded-circle position-absolute" 
                onclick="dismissAIBuddy()"
                style="top: -10px; right: -5px; width: 22px; height: 22px; padding: 0; font-size: 10px; z-index: 10; border: 2px solid white;">
            <i class="bi bi-x"></i>
        </button>
        <button id="ai-chat-trigger" class="btn btn-warning rounded-circle shadow-lg d-flex align-items-center justify-content-center animate__animated animate__pulse animate__infinite" 
                onclick="toggleAIChat()" style="width: 60px; height: 60px; border: 4px solid white;">
            <i class="bi bi-robot fs-3 text-dark"></i>
        </button>
    </div>
</div>

<script>
    function dismissAIBuddy() {
        const wrapper = document.getElementById('ai-assistant-wrapper');
        const ghost = document.getElementById('ai-ghost-tab');
        
        wrapper.style.transform = 'translateX(150px)';
        wrapper.style.opacity = '0';
        setTimeout(() => {
            wrapper.classList.add('d-none');
            ghost.classList.remove('d-none');
            ghost.classList.add('animate__animated', 'animate__slideInRight');
        }, 500);
    }

    function reviveAIBuddy() {
        const wrapper = document.getElementById('ai-assistant-wrapper');
        const ghost = document.getElementById('ai-ghost-tab');
        
        ghost.classList.add('animate__slideOutRight');
        setTimeout(() => {
            ghost.classList.add('d-none');
            ghost.classList.remove('animate__slideOutRight');
            wrapper.classList.remove('d-none');
            setTimeout(() => {
                wrapper.style.transform = 'translateX(0)';
                wrapper.style.opacity = '1';
            }, 10);
        }, 300);
    }

    function toggleAIChat() {
        const win = document.getElementById('ai-chat-window');
        const trigger = document.getElementById('ai-chat-trigger');
        const dismissBtn = document.getElementById('ai-chat-dismiss');
        
        if (win.classList.contains('d-none')) {
            win.classList.remove('d-none');
            win.classList.add('animate__fadeInUp');
            trigger.classList.remove('animate__infinite');
            dismissBtn.classList.add('d-none');
            document.getElementById('ai-chat-input').focus();
        } else {
            win.classList.add('animate__fadeOutDown');
            setTimeout(() => {
                win.classList.remove('animate__fadeOutDown');
                win.classList.add('d-none');
                trigger.classList.add('animate__infinite');
                dismissBtn.classList.remove('d-none');
            }, 300);
        }
    }

    function handleChatEnter(e) {
        if (e.key === 'Enter') sendAIChat();
    }

    async function sendAIChat() {
        const input = document.getElementById('ai-chat-input');
        const body = document.getElementById('ai-chat-body');
        const msg = input.value.trim();
        
        if (!msg) return;

        // Append User Message
        const userDiv = document.createElement('div');
        userDiv.className = 'user-msg mb-4 text-end';
        userDiv.innerHTML = `
            <div class="p-2 px-3 rounded-4 bg-primary text-white small shadow-sm d-inline-block" style="max-width: 85%;">
                ${msg}
            </div>
        `;
        body.appendChild(userDiv);
        input.value = '';
        body.scrollTop = body.scrollHeight;

        // Append Loading Typing
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'ai-loading';
        loadingDiv.className = 'ai-msg mb-4';
        loadingDiv.innerHTML = `
            <div class="p-2 px-3 rounded-4 bg-light small shadow-sm d-inline-block">
                <div class="spinner-grow spinner-grow-sm text-warning" role="status"></div> Sedang berpikir...
            </div>
        `;
        body.appendChild(loadingDiv);
        body.scrollTop = body.scrollHeight;

        try {
            const resp = await fetch('/api/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: msg })
            });
            const data = await resp.json();
            
            if(document.getElementById('ai-loading')) document.getElementById('ai-loading').remove();
            
            // Append AI Message
            const aiDiv = document.createElement('div');
            aiDiv.className = 'ai-msg mb-4';
            aiDiv.innerHTML = `
                <div class="p-2 px-3 rounded-4 bg-light small shadow-sm d-inline-block" style="max-width: 85%;">
                    ${data.reply}
                </div>
            `;
            body.appendChild(aiDiv);
            body.scrollTop = body.scrollHeight;

        } catch (e) {
            if(document.getElementById('ai-loading')) document.getElementById('ai-loading').remove();
            const errDiv = document.createElement('div');
            errDiv.className = 'text-center small text-danger mb-4 opacity-75';
            errDiv.innerHTML = 'Maaf, koneksi terputus. Coba lagi nanti!';
            body.appendChild(errDiv);
        }
    }
</script>

<style>
    #ai-assistant-wrapper .rounded-4 { border-radius: 18px !important; }
    #ai-chat-body::-webkit-scrollbar { width: 4px; }
    #ai-chat-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    
    @media (max-width: 767px) {
        #ai-assistant-wrapper { bottom: 85px !important; right: 15px !important; }
        #ai-chat-window { width: calc(100vw - 30px) !important; max-width: 350px; }
        #ai-ghost-tab { bottom: 100px !important; }
    }
</style>
