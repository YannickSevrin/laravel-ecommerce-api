<form action="{{ $route }}" method="POST" class="space-y-4">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div>
        <label class="block font-semibold">Category name</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" class="border rounded w-full p-2">
    </div>

    <div>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
    </div>
</form>
