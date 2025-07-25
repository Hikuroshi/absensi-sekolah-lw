<x-layout.app :$title>
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="glass-card rounded-xl p-6 text-center">
        <h2 class="text-xl font-semibold">{{ $title }}</h2>
        <p class="mb-6 text-sm text-white/60">
            Silahkan download template lalu isi dengan data yang dibutuhkan agar data konsisten dan masuk dengan benar.
        </p>

        <form
            x-data
            x-ref="form"
            action="{{ route('import-teacher.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="flex flex-col gap-3 text-center sm:flex-row sm:items-center sm:justify-center sm:gap-6">
                <a class="flex w-full items-center justify-center whitespace-nowrap rounded-lg bg-purple-600 px-4 py-3 text-sm font-medium text-white hover:bg-purple-700 sm:w-auto" href="{{ asset('import-template/teacher-template.xlsx') }}" download>
                    <x-lucide-download class="mr-2 h-5 w-5" />
                    Download Template
                </a>

                <button class="flex w-full items-center justify-center whitespace-nowrap rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700 sm:w-auto" type="button" x-on:click="$refs.file.click()">
                    <x-lucide-upload class="mr-2 h-5 w-5" />
                    Import Data
                </button>

                <input
                    id="file"
                    name="file"
                    type="file"
                    accept=".xlsx,.xls,.csv"
                    required
                    hidden
                    x-ref="file"
                    x-on:change="$refs.form.submit()"
                >
            </div>

            <p class="mt-2 text-sm text-gray-300">Format file: .xlsx, .xls, atau .csv</p>
        </form>
    </div>
</x-layout.app>
