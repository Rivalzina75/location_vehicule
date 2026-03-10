<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Liste des documents de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();

        $documents = Document::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Grouper par statut
        $pending = $documents->where('status', 'pending');
        $approved = $documents->where('status', 'approved');
        $rejected = $documents->where('status', 'rejected');

        return view('dashboard.documents', compact('documents', 'pending', 'approved', 'rejected'));
    }

    /**
     * Uploader un nouveau document
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:driving_license,identity_card,passport,credit_card_proof,address_proof,insurance,other',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'expiry_date' => 'nullable|date|after:today',
        ]);

        try {
            $file = $request->file('file');

            // Générer un nom unique
            $filename = time().'_'.$file->getClientOriginalName();

            // Stocker dans storage/app/public/documents
            $path = $file->storeAs('documents', $filename, 'public');

            // Créer l'enregistrement
            $document = Document::create([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'status' => 'pending',
                'expiry_date' => $request->expiry_date,
            ]);

            ActivityLog::log(
                Auth::id(),
                'document_uploaded',
                __('Document uploadé'),
                $document->type_label.' - '.$document->filename,
                ['document_id' => $document->id, 'type' => $document->type]
            );

            return back()->with('success', __('Document uploadé avec succès !'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'upload: '.$e->getMessage());
        }
    }

    /**
     * Supprimer un document
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        // Ne pas supprimer un document approuvé
        if ($document->status === 'approved') {
            return back()->with('error', 'Impossible de supprimer un document approuvé.');
        }

        try {
            // Supprimer le fichier
            Storage::disk('public')->delete($document->path);

            // Supprimer l'enregistrement
            $document->delete();

            return back()->with('success', 'Document supprimé.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: '.$e->getMessage());
        }
    }
}
