@extends('layouts.app') <!-- Use your main layout -->

@section('title', 'Profile') <!-- Optional page title -->

@section('content')

<style>
    /* ================================================
   PROFILE PAGE — FULL MOBILE RESPONSIVE STYLING
   Works from 360px up
   No HTML changes
   ================================================ */

    /* Cards */
    .bg-white.shadow.rounded-lg {
        background-color: #ffffff !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06) !important;
        padding: 24px !important;
        margin-bottom: 20px !important;
    }

    /* Card header spacing */
    header h2 {
        font-size: 20px !important;
        font-weight: 600 !important;
        margin-bottom: 6px !important;
    }

    header p {
        font-size: 14px !important;
        color: #555 !important;
        line-height: 1.5 !important;
    }

    /* Form fields */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    textarea,
    select {
        width: 100% !important;
        padding: 10px 12px !important;
        font-size: 14px !important;
        border: 1px solid #ccc !important;
        border-radius: 6px !important;
        box-sizing: border-box;
        margin-top: 6px !important;
        margin-bottom: 10px !important;
    }

    /* Buttons */
    .flex.items-center.gap-4 button,
    .x-primary-button,
    .x-secondary-button,
    .x-danger-button {
        padding: 10px 18px !important;
        font-size: 14px !important;
        border-radius: 6px !important;
        min-width: 120px;
        cursor: pointer;
    }

    /* Button spacing in flex */
    .flex.items-center.gap-4 {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
    }

    /* Delete modal */
    .x-modal form {
        width: 100% !important;
        max-width: 400px !important;
        margin: auto;
        padding: 20px !important;
    }

    /* Modal buttons */
    .x-modal .flex.justify-end {
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
        gap: 10px;
    }

    /* Success/Error messages */
    .text-sm.text-gray-600,
    .text-sm.text-green-600 {
        font-size: 13px !important;
        margin-top: 4px !important;
    }

    /* ==================================================
   Responsive for Mobile 480px and below
   ================================================== */
    @media (max-width: 480px) {

        .container.py-12 {
            padding: 15px 10px !important;
        }

        header h2 {
            font-size: 18px !important;
        }

        header p {
            font-size: 13px !important;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            font-size: 13px !important;
            padding: 8px 10px !important;
        }

        .flex.items-center.gap-4 button,
        .x-primary-button,
        .x-secondary-button,
        .x-danger-button {
            width: 100% !important;
            text-align: center;
        }

        .flex.items-center.gap-4 {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        .x-modal form {
            width: 90% !important;
            padding: 16px !important;
        }

        .x-modal h2 {
            font-size: 16px !important;
        }
    }

    .flex.items-center.gap-4 button {
        background: #11142d;
        color: #ffffff !important;
    }

    button.inline-flex.items-center.px-4.py-2.bg-red-600.border.border-transparent.rounded-md.font-semibold.text-xs.text-white.uppercase.tracking-widest.hover\:bg-red-500.active\:bg-red-700.focus\:outline-none.focus\:ring-2.focus\:ring-red-500.focus\:ring-offset-2.dark\:focus\:ring-offset-gray-800.transition.ease-in-out.duration-150 {
        background: red;
        color: #ffffff !important;
    }

    /* ==================================================
   Extra Small Devices 360px
   ================================================== */
    @media (max-width: 360px) {

        header h2 {
            font-size: 16px !important;
        }

        header p {
            font-size: 12px !important;
        }

        .bg-white.shadow.rounded-lg {
            padding: 16px !important;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            font-size: 12px !important;
            padding: 7px 8px !important;
        }

        .flex.items-center.gap-4 button,
        .x-primary-button,
        .x-secondary-button,
        .x-danger-button {
            font-size: 13px !important;
            padding: 9px !important;
        }

        .flex.items-center.gap-4 {
            gap: 8px !important;
        }

        .x-modal form {
            width: 95% !important;
            padding: 14px !important;
        }

        .x-modal .flex.justify-end {
            flex-direction: column !important;
            gap: 8px !important;
        }
    }
</style>

<div class="container py-12">

    <div class="space-y-6">

        <!-- Update Profile Information -->
        <div class="p-4 bg-white shadow rounded-lg">
            <div class="max-w-xl mx-auto">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="p-4 bg-white shadow rounded-lg">
            <div class="max-w-xl mx-auto">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete User Account -->
        <div class="p-4 bg-white shadow rounded-lg">
            <div class="max-w-xl mx-auto">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</div>
@endsection