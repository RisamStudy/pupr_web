<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Oops! Something went wrong</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.11.5/dist/gsap.min.js"></script>
</head>
<body class="h-full bg-gradient-to-br from-indigo-100 to-purple-200 flex items-center justify-center">
    <div class="text-center px-4 lg:px-40 py-8 lg:py-16">
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="mb-4 text-4xl font-extrabold text-gray-900 md:text-5xl lg:text-6xl">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">500</span> Error
        </h1>
        <p class="mb-8 text-lg font-normal text-gray-600 lg:text-xl">
            Oops! Our server is feeling a bit under the weather.
        </p>
        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
            <a href="{{ url('/') }}" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300">
                Go back home
                <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </a>
            <a href="#" onclick="window.location.reload()" class="inline-flex justify-center hover:text-gray-900 items-center py-3 px-5 text-base font-medium text-center text-gray-600 rounded-lg border border-indigo-600 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100">
                Try again
            </a>
        </div>
    </div>

    <script>
        gsap.from("svg", {duration: 1, y: -50, opacity: 0, ease: "bounce"});
        gsap.from("h1", {duration: 1, x: -100, opacity: 0, delay: 0.5});
        gsap.from("p", {duration: 1, x: 100, opacity: 0, delay: 0.8});
        gsap.from("a", {duration: 1, y: 50, opacity: 0, stagger: 0.2, delay: 1});
    </script>
</body>
</html>
