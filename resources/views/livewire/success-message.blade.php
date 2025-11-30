<div class="p-6 max-w-md mx-auto bg-white dark:bg-gray-900 shadow rounded-lg mt-10 text-center">
    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Hongera!</h2>
    <p class="text-gray-800 dark:text-gray-100 mb-2">
        Ndugu <strong>{{ $user->name }}</strong>, umejisajili kwa kikundi <strong>{{ $group->name }}</strong>.
    </p>
    <p class="text-gray-800 dark:text-gray-100">
        Code yako ya kuingia: <strong>{{ $user->login_code }}</strong>
    </p>
    <p class="text-gray-800 dark:text-gray-100 mt-2">
        Kiasi cha kuchangia: Tsh <strong>{{ number_format($group->contribution_amount, 2) }}</strong>
    </p>
</div>
