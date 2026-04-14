<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        $pendingBusinesses = Business::where('status', 'pending')->with('owner')->latest()->get();
        $approvedBusinesses = Business::where('status', 'approved')->with('owner')->latest()->paginate(20);
        
        return view('admin.business.index', compact('pendingBusinesses', 'approvedBusinesses'));
    }

    public function approve(Business $business)
    {
        $business->update(['status' => 'approved']);
        return back()->with('success', "Usaha '{$business->name}' telah disetujui dan kini tayang di Marketplace!");
    }

    public function reject(Business $business)
    {
        // For now we just set back to pending or we could delete/soft-delete
        // Let's set to 'rejected' if we had that status, but for simplicity let's delete for now
        // Or just keep as pending but with a note? 
        // Let's just delete to keep it clean (User can re-register)
        $business->delete(); 
        return back()->with('info', "Pendaftaran usaha telah ditolak dan dihapus.");
    }
}
