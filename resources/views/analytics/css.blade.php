<style>
    /* ============================================
   GLOBAL SUMMARY PAGE STYLES
=============================================*/
    .summary-box {
        font-size: 15px;
        line-height: 30px;
        color: #11142d;
        padding: 20px 15px;
    }

    .summary-box strong {
        font-weight: 700;
        color: #11142d;
    }

    .section-title {
        font-weight: 800;
        font-size: 26px;
        color: #4d75e3;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0;
    }

    .label-line {
        display: inline-block;
        border-bottom: 2px dotted #999;
        margin: 0 6px;
        width: 150px;
    }

    .percent-value {
        font-weight: 800;
        color: #d32f2f;
    }

    /* ============================================
   BUTTONS (UNCHANGED)
=============================================*/
    .btn-primary,
    .btn-warning,
    .btn-danger {
        min-width: 110px;
        padding: 10px 14px;
        font-weight: 600;
    }

    /* ============================================
   CHART SECTION STYLING
=============================================*/
    .chart-container {
        width: 100%;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
        padding: 10px;
    }

    .chart-card {
        background: #ffffff;
        padding: 20px 15px;
        border-radius: 16px;
        text-align: center;
        width: 100%;
    }

    .chart-card canvas {
        max-height: 280px !important;
        width: 100% !important;
        height: auto !important;
    }

    canvas {
        aspect-ratio: 1 / 1 !important;
    }

    /* ============================================
   RESPONSIVE FIXES (TABLETS)
=============================================*/
    @media (max-width: 992px) {
        .chart-container {
            height: auto;
            padding: 20px 0;
        }

        .border-end {
            border-right: none !important;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
    }

    /* ============================================
   MOBILE FIXES (max-width: 768px)
=============================================*/
    @media (max-width: 768px) {

        .section-title {
            /* text-align: center; */
            font-size: 22px;
        }

        .summary-box {
            font-size: 15px;
            line-height: 35px;
            padding: 15px;
        }

        .chart-container {
            padding-top: 10px;
        }

        .chart-card {
            padding: 15px;
        }

        .chart-card canvas {
            max-height: 240px !important;
        }

        .label-line {
            width: 40px;
        }

        /* Filter buttons stack neatly */
        .filter-btn-wrapper {
            text-align: center !important;
            margin-top: 10px;
        }

        .filter-btn-wrapper .btn {
            margin-bottom: 6px;
            width: 100%;
        }
    }

    /* ============================================
   SUPER SMALL SCREENS (max-width: 400px)
=============================================*/
    @media (max-width: 400px) {

        /* Fix overall layout */
        #reportContent {
            padding: 0 8px;
        }

        .section-title {
            font-size: 18px;
            margin-bottom: 0;
        }

        .summary-box {
            font-size: 14px;
            line-height: 40px;
            padding: 12px 10px;
        }

        /* Chart size optimized */
        .chart-container {
            padding: 0;
            margin-top: 10px;
        }

        .chart-card {
            padding: 10px;
        }

        /* .chart-card canvas {
            max-height: 180px !important;
        } */

        .label-line {
            width: 20px;
        }

        /* Buttons perfect stack */
        .filter-btn-wrapper .btn {
            width: 100%;
            margin-bottom: 7px;
            font-size: 14px;
        }

        /* Fix spacing */
        .border-end {
            border: none !important;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }
    }
</style>