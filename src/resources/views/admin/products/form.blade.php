<form action="{{ $route }}" method="POST" class="space-y-4">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div>
        <label class="block">Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="border rounded w-full p-2">
    </div>

    <div>
        <label class="block">Price</label>
        <input type="text" name="price" value="{{ old('price', $product->price ?? '') }}" class="border rounded w-full p-2">
    </div>

    <div>
        <label class="block">Category</label>
        <select name="category_id" class="border rounded w-full p-2">
            <option value="">-- Choose --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @if(old('category_id', $product->category_id ?? '') == $category->id) selected @endif>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block">Description</label>
        <textarea name="description" class="border rounded w-full p-2">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
    </div>
</form>
