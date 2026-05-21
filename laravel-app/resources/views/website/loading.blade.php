<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generating Website...</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh;
        }
        .card {
            text-align: center; padding: 60px 48px;
            background: #1e293b; border-radius: 20px;
            max-width: 480px; width: 90%;
            box-shadow: 0 32px 64px rgba(0,0,0,0.4);
        }
        .spinner {
            width: 64px; height: 64px;
            border: 4px solid rgba(99,102,241,0.2);
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            margin: 0 auto 32px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        h2  { font-size: 1.5rem; font-weight: 700; margin-bottom: 12px; }
        p   { color: #94a3b8; font-size: 0.95rem; line-height: 1.6; margin-bottom: 32px; }
        .status-badge {
            display: inline-block;
            background: rgba(99,102,241,0.15); color: #818cf8;
            padding: 6px 18px; border-radius: 999px;
            font-size: 0.82rem; font-weight: 600; letter-spacing: 0.05em;
        }
        .steps {
            margin: 28px 0; text-align: left;
            display: flex; flex-direction: column; gap: 12px;
        }
        .step {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 10px;
            background: rgba(255,255,255,0.04);
            font-size: 0.88rem; color: #94a3b8;
            transition: all 0.3s;
        }
        .step.active { background: rgba(99,102,241,0.12); color: #c7d2fe; }
        .step.done   { color: #86efac; }
        .step-icon   { font-size: 1.1rem; flex-shrink: 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner" id="spinner"></div>
        <h2>AI is crafting your website</h2>
        <p>Claude is analyzing the business data and generating a unique design, theme, and content tailored for this business.</p>

        <div class="steps" id="steps">
            <div class="step active" id="step-1">
                <span class="step-icon">🔍</span>
                <span>Analyzing business type & category</span>
            </div>
            <div class="step" id="step-2">
                <span class="step-icon">🎨</span>
                <span>Selecting theme, colors & fonts</span>
            </div>
            <div class="step" id="step-3">
                <span class="step-icon">✍️</span>
                <span>Writing personalized content</span>
            </div>
            <div class="step" id="step-4">
                <span class="step-icon">⚡</span>
                <span>Finalizing layout & sections</span>
            </div>
        </div>

        <div class="status-badge" id="status-text">Generating...</div>
    </div>

    <script>
        const slug   = @json($site->slug);
        const pollUrl = `/dashboard/generated-site/${slug}/status`;

        // Animate steps
        const steps = ['step-1','step-2','step-3','step-4'];
        let   current = 0;
        const stepTimer = setInterval(() => {
            if (current > 0) {
                document.getElementById(steps[current - 1]).classList.remove('active');
                document.getElementById(steps[current - 1]).classList.add('done');
                document.getElementById(steps[current - 1]).querySelector('.step-icon').textContent = '✅';
            }
            if (current < steps.length) {
                document.getElementById(steps[current]).classList.add('active');
                current++;
            } else {
                clearInterval(stepTimer);
            }
        }, 4000);

        // Poll for completion
        let attempts = 0;
        const poll = setInterval(async () => {
            attempts++;
            try {
                const res  = await fetch(pollUrl);
                const data = await res.json();

                if (data.done) {
                    clearInterval(poll);
                    clearInterval(stepTimer);
                    document.getElementById('status-text').textContent = 'Done! Redirecting...';
                    document.getElementById('spinner').style.borderTopColor = '#22c55e';
                    setTimeout(() => window.location.reload(), 800);
                }

                if (data.status === 'failed') {
                    clearInterval(poll);
                    clearInterval(stepTimer);
                    document.getElementById('status-text').textContent = 'Generation failed.';
                    document.getElementById('spinner').style.borderTopColor = '#ef4444';
                }
            } catch (e) {
                // network error — keep polling
            }

            if (attempts > 60) { // 2 min timeout
                clearInterval(poll);
                document.getElementById('status-text').textContent = 'Taking longer than expected...';
            }
        }, 2000);
    </script>
</body>
</html>