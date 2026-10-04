<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingCategoryId = null;
    public string $name = '';
    public string $description = '';

    public bool $showDeleteModal = false;
    public ?int $deletingCategoryId = null;

    protected function rules(): array
    {
        $nameRule = 'required|string|max:255|unique:categories,name';
        if ($this->editingCategoryId) {
            $nameRule .= ',' . $this->editingCategoryId;
        }

        return [
            'name' => $nameRule,
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Category::class);

        $this->resetValidation();
        $this->reset(['editingCategoryId', 'name', 'description']);
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $this->resetValidation();
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = (string) $category->description;
        $this->showModal = true;
    }

    public function saveCategory(): void
    {
        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $this->authorize('update', $category);
        } else {
            $this->authorize('create', Category::class);
        }

        $validated = $this->validate();

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $category->update($validated);
            session()->flash('message', 'Category updated successfully.');
        } else {
            Category::create($validated);
            session()->flash('message', 'Category created successfully.');
        }

        $this->showModal = false;
    }

    public function confirmDelete(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);

        if ($category->products()->count() > 0) {
            session()->flash('error', "Cannot delete category '{$category->name}' because {$category->products()->count()} product(s) belong to it.");
            return;
        }

        $this->deletingCategoryId = $category->id;
        $this->showDeleteModal = true;
    }

    public function deleteCategory(): void
    {
        if ($this->deletingCategoryId) {
            $category = Category::findOrFail($this->deletingCategoryId);
            $this->authorize('delete', $category);

            if ($category->products()->count() > 0) {
                session()->flash('error', 'Cannot delete category with associated products.');
            } else {
                $category->delete();
                session()->flash('message', 'Category deleted successfully.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingCategoryId = null;
    }

    public function render(): View
    {
        $query = Category::withCount('products')->orderBy('name');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.categories-list', [
            'categories' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
