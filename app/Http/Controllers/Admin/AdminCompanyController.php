<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCompanyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Company::withCount('users', 'people');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');

        if (in_array($sortBy, ['name', 'created_at', 'users_count', 'people_count'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $companies = $query->paginate(25)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function show(Company $company): View
    {
        $company->load(['users', 'people' => function ($q) {
            $q->active()->orderBy('name');
        }]);

        return view('admin.companies.show', compact('company'));
    }
}
