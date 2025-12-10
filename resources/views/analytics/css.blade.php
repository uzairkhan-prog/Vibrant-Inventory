<style>
    /* ============================================================
    GLOBAL SUMMARY PAGE STYLES (SAFE TO KEEP WITH YOUR EXISTING)
    ===============================================================*/
    .summary-box {
        font-size: 15px;
        line-height: 34px;
        color: #11142d;
        padding: 25px 20px;
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
        margin-bottom: 25px;
    }

    .label-line {
        display: inline-block;
        width: 150px;
        border-bottom: 2px dotted #999;
        margin: 0 6px;
    }

    .percent-value {
        font-weight: 800;
        color: #d32f2f;
    }

    /* ============================================================
    BUTTONS
    ===============================================================*/
    .btn-primary,
    .btn-warning,
    .btn-danger {
        min-width: 110px;
        padding: 10px 14px;
        font-weight: 600;
    }

    /* ============================================================
   CHART FIX — *** MAIN SOLUTION ***
===============================================================*/

    /* Parent container always controls spacing */
    .chart-container {
        width: 100%;
        height: 350px;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        margin: 0 auto;
    }

    /* Chart card wrapper */
    .chart-card {
        background: #ffffff;
        padding: 20px 15px;
        border-radius: 16px;
        text-align: center;
        width: 100%;
    }

    /* Chart header */
    .chart-card h5 {
        font-weight: 700;
        color: #11142d;
    }

    /* Canvas chart styling */
    .chart-card canvas {
        max-height: 320px !important;
        width: 100% !important;
        height: auto !important;
    }

    /* KEEP CHART PERFECTLY CIRCLE */
    canvas {
        aspect-ratio: 1 / 1 !important;
    }

    /* ============================================================
    RESPONSIVE CHART FIXES
    ===============================================================*/

    /* Tablets */
    @media (max-width: 992px) {

        .chart-container {
            height: 300px;
            margin-top: 20px;
        }

        .border-end {
            border-right: none !important;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
    }

    /* Mobiles */
    @media (max-width: 768px) {

        .chart-container {
            height: auto;
            padding: 20px 0;
        }

        .label-line {
            width: 120px;
        }
    }

    /* Extra small phones */
    @media (max-width: 480px) {

        .section-title {
            font-size: 20px;
            text-align: center;
        }

        .summary-box {
            font-size: 16px;
            line-height: 30px;
        }

        .label-line {
            width: 80px;
        }
    }
</style>