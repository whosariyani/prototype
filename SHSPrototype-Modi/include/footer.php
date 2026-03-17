<style>
    :root {
        --bg: #f4f6f8;
        --card: #ffffff;
        --muted: #6b7280;
        --green-1: #095d2fff;
        --green-2: #1fa25a;
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
        --text: #0f172a;
    }

    footer {
        background: linear-gradient(90deg, var(--green-1), var(--green-2));
        color: #fff;
        padding: clamp(12px, 3vw, 18px) clamp(16px, 5vw, 28px);
        gap: 16px;
        box-shadow: var(--shadow-sm);
        position: relative;
        z-index: 40;
        text-align: center;
        margin-top: 40px;
        font-size: clamp(12px, 2.5vw, 14px);
        line-height: 1.5;
    }

    footer a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: all 200ms ease;
        border-bottom: 1px solid transparent;
    }

    footer a:hover {
        color: #fff;
        border-bottom-color: rgba(255, 255, 255, 0.5);
    }

    /* Dark mode support */
    body.dark footer {
        background: linear-gradient(90deg, #095d2fff, #1fa25a);
    }

    /* Responsive footer */
    @media (max-width: 768px) {
        footer {
            padding: 10px 14px;
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        footer {
            padding: 8px 12px;
            font-size: 11px;
            margin-top: 30px;
        }
    }

    @media (max-width: 360px) {
        footer {
            padding: 6px 10px;
            font-size: 10px;
        }
    }
</style>
<footer>
    <p style="margin: 0;">
        &copy; 2026 Entrance Monitoring System. All rights reserved.
    </p>
</footer>
