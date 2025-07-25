<!-- Loading Skeleton para Cards do Dashboard -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
    @for($i = 0; $i < 6; $i++)
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 p-6">
        <!-- Header skeleton -->
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gray-200 rounded-xl skeleton"></div>
            <div class="ml-4 flex-1">
                <div class="h-6 bg-gray-200 rounded-lg skeleton mb-2" style="width: 60%;"></div>
                <div class="h-4 bg-gray-200 rounded skeleton" style="width: 40%;"></div>
            </div>
        </div>

        <!-- Description skeleton -->
        <div class="mb-6">
            <div class="h-4 bg-gray-200 rounded skeleton mb-2"></div>
            <div class="h-4 bg-gray-200 rounded skeleton" style="width: 70%;"></div>
        </div>

        <!-- Buttons skeleton -->
        <div class="flex space-x-3">
            <div class="flex-1 h-12 bg-gray-200 rounded-xl skeleton"></div>
            <div class="w-24 h-12 bg-gray-200 rounded-xl skeleton"></div>
        </div>

        <!-- Progress bar skeleton -->
        <div class="mt-4 h-1 bg-gray-200 rounded-full skeleton"></div>
    </div>
    @endfor
</div>

<!-- Loading Skeleton para Cards de Sentimentos -->
<div class="space-y-4" id="sentimentsSkeleton" style="display: none;">
    @for($i = 0; $i < 5; $i++)
    <div class="bg-white rounded-xl shadow-sm border-l-4 border-l-gray-300 p-6">
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-200 rounded-full skeleton"></div>
                <div>
                    <div class="h-6 bg-gray-200 rounded skeleton mb-2" style="width: 120px;"></div>
                    <div class="h-4 bg-gray-200 rounded skeleton" style="width: 80px;"></div>
                </div>
            </div>
            <div class="w-16 h-6 bg-gray-200 rounded-full skeleton"></div>
        </div>

        <div class="mb-3">
            <div class="h-4 bg-gray-200 rounded skeleton mb-2"></div>
            <div class="h-4 bg-gray-200 rounded skeleton" style="width: 60%;"></div>
        </div>

        <div class="flex justify-between items-center">
            <div class="h-4 bg-gray-200 rounded skeleton" style="width: 100px;"></div>
            <div class="flex space-x-2">
                <div class="w-8 h-8 bg-gray-200 rounded skeleton"></div>
                <div class="w-8 h-8 bg-gray-200 rounded skeleton"></div>
            </div>
        </div>
    </div>
    @endfor
</div>

<!-- Loading Skeleton para Formulários -->
<div class="space-y-6" id="formSkeleton" style="display: none;">
    <div class="space-y-2">
        <div class="h-4 bg-gray-200 rounded skeleton" style="width: 100px;"></div>
        <div class="h-12 bg-gray-200 rounded-xl skeleton"></div>
    </div>

    <div class="space-y-2">
        <div class="h-4 bg-gray-200 rounded skeleton" style="width: 120px;"></div>
        <div class="h-12 bg-gray-200 rounded-xl skeleton"></div>
    </div>

    <div class="space-y-2">
        <div class="h-4 bg-gray-200 rounded skeleton" style="width: 140px;"></div>
        <div class="h-24 bg-gray-200 rounded-xl skeleton"></div>
    </div>

    <div class="flex space-x-3">
        <div class="flex-1 h-12 bg-gray-200 rounded-xl skeleton"></div>
        <div class="flex-1 h-12 bg-gray-200 rounded-xl skeleton"></div>
    </div>
</div>

<script>
// Funções para mostrar/esconder skeletons
function showDashboardSkeleton() {
    const skeleton = document.getElementById('dashboardSkeleton');
    if (skeleton) skeleton.style.display = 'block';
}

function hideDashboardSkeleton() {
    const skeleton = document.getElementById('dashboardSkeleton');
    if (skeleton) skeleton.style.display = 'none';
}

function showSentimentsSkeleton() {
    const skeleton = document.getElementById('sentimentsSkeleton');
    if (skeleton) skeleton.style.display = 'block';
}

function hideSentimentsSkeleton() {
    const skeleton = document.getElementById('sentimentsSkeleton');
    if (skeleton) skeleton.style.display = 'none';
}

function showFormSkeleton() {
    const skeleton = document.getElementById('formSkeleton');
    if (skeleton) skeleton.style.display = 'block';
}

function hideFormSkeleton() {
    const skeleton = document.getElementById('formSkeleton');
    if (skeleton) skeleton.style.display = 'none';
}
</script>
