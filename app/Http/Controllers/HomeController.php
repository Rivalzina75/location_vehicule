<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Stats dynamiques
        $availableVehicles = Vehicle::where('status', 'available')->count();
        $activeReservations = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['active', 'confirmed'])
            ->count();
        $completedReservations = Reservation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Note moyenne des véhicules loués par cet utilisateur
        $avgRating = Vehicle::whereHas('reservations', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'completed');
        })->avg('rating');
        $avgRating = $avgRating ? round($avgRating, 1) : null;

        // 3 prochaines réservations (confirmées ou actives, triées par date)
        $upcomingReservations = Reservation::with('vehicle')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->limit(3)
            ->get();

        // Activité récente (max 3)
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Astuces du jour (rotation)
        $tips = [
            __('Pensez à vérifier le niveau de carburant avant chaque départ pour éviter des frais supplémentaires !'),
            __('Réservez à l\'avance pour bénéficier des meilleurs tarifs et de la plus grande disponibilité.'),
            __('N\'oubliez pas de prendre des photos lors de l\'inspection pour votre sécurité.'),
            __('Les locations longue durée (semaine/mois) offrent des tarifs préférentiels.'),
            __('Gardez vos documents à jour pour accélérer le processus de location.'),
        ];
        $tipIndex = (now()->dayOfYear + now()->hour + (int) $user->id) % count($tips);
        $tipOfTheDay = $tips[$tipIndex];

        return view('dashboard', compact(
            'availableVehicles',
            'activeReservations',
            'completedReservations',
            'avgRating',
            'upcomingReservations',
            'recentActivities',
            'tipOfTheDay'
        ));
    }

    /**
     * Afficher l'historique d'activité complet
     */
    public function activity()
    {
        $user = Auth::user();

        $activities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.activity', compact('activities'));
    }
}
