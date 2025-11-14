@extends('layouts.app')

@section('content')
<div class="dashboard-wrapper">
    <!-- SIDEBAR NAVIGATION -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-header">
            <h2>🚗 Machina</h2>
            <p>Tableau de Bord</p>
        </div>

        <nav class="sidebar-nav">
            <a href="#accueil" class="sidebar-link active" data-section="accueil">
                <span class="icon">🏠</span> Accueil
            </a>
            <a href="#catalogue" class="sidebar-link" data-section="catalogue">
                <span class="icon">🚙</span> Catalogue de Véhicules
            </a>
            <a href="#reservation" class="sidebar-link" data-section="reservation">
                <span class="icon">📝</span> Nouvelle Réservation
            </a>
            <a href="#mes-reservations" class="sidebar-link" data-section="mes-reservations">
                <span class="icon">📋</span> Mes Réservations
            </a>
            <a href="#documents" class="sidebar-link" data-section="documents">
                <span class="icon">📄</span> Mes Documents
            </a>
            <a href="#inspection" class="sidebar-link" data-section="inspection">
                <span class="icon">📸</span> Inspection Véhicule
            </a>
            <a href="#profil" class="sidebar-link" data-section="profil">
                <span class="icon">👤</span> Mon Profil
            </a>
        </nav>

        <div class="sidebar-footer">
            <p class="user-info">
                <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong>
            </p>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="dashboard-main">

        <!-- SECTION ACCUEIL -->
        <section id="accueil" class="dashboard-section active">
            <div class="section-header">
                <h1>Bienvenue {{ Auth::user()->first_name }} !</h1>
                <p>Gérez vos locations de véhicules en toute simplicité</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">🚗</div>
                    <div class="stat-info">
                        <h3>12</h3>
                        <p>Véhicules disponibles</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <h3>2</h3>
                        <p>Réservations actives</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <h3>5</h3>
                        <p>Locations terminées</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-info">
                        <h3>4.8/5</h3>
                        <p>Note moyenne</p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Actions rapides</h2>
                <div class="actions-grid">
                    <button class="action-btn" onclick="navigateTo('catalogue')">
                        <span class="action-icon">🚙</span>
                        <span class="action-text">Voir les véhicules</span>
                    </button>
                    <button class="action-btn" onclick="navigateTo('reservation')">
                        <span class="action-icon">📝</span>
                        <span class="action-text">Réserver maintenant</span>
                    </button>
                    <button class="action-btn" onclick="navigateTo('documents')">
                        <span class="action-icon">📄</span>
                        <span class="action-text">Gérer mes documents</span>
                    </button>
                    <button class="action-btn" onclick="navigateTo('inspection')">
                        <span class="action-icon">📸</span>
                        <span class="action-text">Inspection véhicule</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- SECTION CATALOGUE -->
        <section id="catalogue" class="dashboard-section">
            <div class="section-header">
                <h1>Catalogue de Véhicules</h1>
                <p>Choisissez le véhicule qui correspond à vos besoins</p>
            </div>

            <div class="filter-bar">
                <select class="filter-select" id="type-filter">
                    <option value="">Tous les types</option>
                    <option value="berline">Berline</option>
                    <option value="suv">SUV</option>
                    <option value="moto">Moto</option>
                    <option value="scooter">Scooter</option>
                    <option value="camionnette">Camionnette</option>
                    <option value="camion">Camion</option>
                </select>

                <select class="filter-select" id="tarif-filter">
                    <option value="">Tous les tarifs</option>
                    <option value="jour">À la journée</option>
                    <option value="semaine">À la semaine</option>
                    <option value="mois">Au mois</option>
                </select>
            </div>

            <div class="vehicles-grid">
                <!-- Berline -->
                <div class="vehicle-card" data-type="berline">
                    <div class="vehicle-image">🚗</div>
                    <div class="vehicle-info">
                        <h3>Peugeot 508</h3>
                        <p class="vehicle-type">Berline</p>
                        <div class="vehicle-features">
                            <span>✓ 5 places</span>
                            <span>✓ Climatisation</span>
                            <span>✓ GPS</span>
                        </div>
                        <div class="vehicle-pricing">
                            <div class="price-item">
                                <span class="price-label">Jour</span>
                                <span class="price-value">45€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Semaine</span>
                                <span class="price-value">250€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Mois</span>
                                <span class="price-value">800€</span>
                            </div>
                        </div>
                        <button class="btn-reserve" onclick="selectVehicle('Peugeot 508', 'berline')">Réserver</button>
                    </div>
                </div>

                <!-- SUV -->
                <div class="vehicle-card" data-type="suv">
                    <div class="vehicle-image">🚙</div>
                    <div class="vehicle-info">
                        <h3>Renault Kadjar</h3>
                        <p class="vehicle-type">SUV</p>
                        <div class="vehicle-features">
                            <span>✓ 5 places</span>
                            <span>✓ 4x4</span>
                            <span>✓ Coffre XXL</span>
                        </div>
                        <div class="vehicle-pricing">
                            <div class="price-item">
                                <span class="price-label">Jour</span>
                                <span class="price-value">65€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Semaine</span>
                                <span class="price-value">380€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Mois</span>
                                <span class="price-value">1200€</span>
                            </div>
                        </div>
                        <button class="btn-reserve" onclick="selectVehicle('Renault Kadjar', 'suv')">Réserver</button>
                    </div>
                </div>

                <!-- Moto -->
                <div class="vehicle-card" data-type="moto">
                    <div class="vehicle-image">🏍️</div>
                    <div class="vehicle-info">
                        <h3>Yamaha MT-07</h3>
                        <p class="vehicle-type">Moto</p>
                        <div class="vehicle-features">
                            <span>✓ 2 casques</span>
                            <span>✓ Permis A</span>
                            <span>✓ 700cc</span>
                        </div>
                        <div class="vehicle-pricing">
                            <div class="price-item">
                                <span class="price-label">Jour</span>
                                <span class="price-value">50€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Semaine</span>
                                <span class="price-value">300€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Mois</span>
                                <span class="price-value">900€</span>
                            </div>
                        </div>
                        <button class="btn-reserve" onclick="selectVehicle('Yamaha MT-07', 'moto')">Réserver</button>
                    </div>
                </div>

                <!-- Scooter -->
                <div class="vehicle-card" data-type="scooter">
                    <div class="vehicle-image">🛵</div>
                    <div class="vehicle-info">
                        <h3>Piaggio Liberty</h3>
                        <p class="vehicle-type">Scooter</p>
                        <div class="vehicle-features">
                            <span>✓ 1 casque</span>
                            <span>✓ Permis B</span>
                            <span>✓ 125cc</span>
                        </div>
                        <div class="vehicle-pricing">
                            <div class="price-item">
                                <span class="price-label">Jour</span>
                                <span class="price-value">25€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Semaine</span>
                                <span class="price-value">140€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Mois</span>
                                <span class="price-value">450€</span>
                            </div>
                        </div>
                        <button class="btn-reserve" onclick="selectVehicle('Piaggio Liberty', 'scooter')">Réserver</button>
                    </div>
                </div>

                <!-- Camionnette -->
                <div class="vehicle-card" data-type="camionnette">
                    <div class="vehicle-image">🚐</div>
                    <div class="vehicle-info">
                        <h3>Renault Master</h3>
                        <p class="vehicle-type">Camionnette</p>
                        <div class="vehicle-features">
                            <span>✓ 3 places</span>
                            <span>✓ 12m³</span>
                            <span>✓ Hayon</span>
                        </div>
                        <div class="vehicle-pricing">
                            <div class="price-item">
                                <span class="price-label">Jour</span>
                                <span class="price-value">70€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Semaine</span>
                                <span class="price-value">420€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Mois</span>
                                <span class="price-value">1400€</span>
                            </div>
                        </div>
                        <button class="btn-reserve" onclick="selectVehicle('Renault Master', 'camionnette')">Réserver</button>
                    </div>
                </div>

                <!-- Camion -->
                <div class="vehicle-card" data-type="camion">
                    <div class="vehicle-image">🚚</div>
                    <div class="vehicle-info">
                        <h3>Mercedes Actros</h3>
                        <p class="vehicle-type">Camion</p>
                        <div class="vehicle-features">
                            <span>✓ Permis C</span>
                            <span>✓ 20 tonnes</span>
                            <span>✓ Grue</span>
                        </div>
                        <div class="vehicle-pricing">
                            <div class="price-item">
                                <span class="price-label">Jour</span>
                                <span class="price-value">150€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Semaine</span>
                                <span class="price-value">900€</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Mois</span>
                                <span class="price-value">3000€</span>
                            </div>
                        </div>
                        <button class="btn-reserve" onclick="selectVehicle('Mercedes Actros', 'camion')">Réserver</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION NOUVELLE RÉSERVATION -->
        <section id="reservation" class="dashboard-section">
            <div class="section-header">
                <h1>Nouvelle Réservation</h1>
                <p>Remplissez le formulaire pour réserver votre véhicule</p>
            </div>

            <form class="reservation-form" id="reservationForm">
                @csrf

                <!-- Étape 1: Choix du véhicule -->
                <div class="form-step active" data-step="1">
                    <h2 class="step-title">1. Choix du véhicule</h2>

                    <div class="form-group">
                        <label for="vehicle_type">Type de véhicule *</label>
                        <select id="vehicle_type" name="vehicle_type" class="form-control" required>
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="berline">Berline</option>
                            <option value="suv">SUV</option>
                            <option value="moto">Moto</option>
                            <option value="scooter">Scooter</option>
                            <option value="camionnette">Camionnette</option>
                            <option value="camion">Camion</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="vehicle_model">Modèle du véhicule *</label>
                        <input type="text" id="vehicle_model" name="vehicle_model" class="form-control" readonly>
                    </div>

                    <button type="button" class="btn-next" onclick="nextStep(2)">Suivant →</button>
                </div>

                <!-- Étape 2: Durée et type de contrat -->
                <div class="form-step" data-step="2">
                    <h2 class="step-title">2. Durée de location</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Date de début *</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Date de fin *</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tarif_type">Type de tarif *</label>
                        <select id="tarif_type" name="tarif_type" class="form-control" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="jour">À la journée</option>
                            <option value="semaine">À la semaine</option>
                            <option value="mois">Au mois</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contract_type">Type de contrat *</label>
                        <select id="contract_type" name="contract_type" class="form-control" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="simple">Location simple</option>
                            <option value="pro">Contrat professionnel</option>
                        </select>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn-prev" onclick="prevStep(1)">← Précédent</button>
                        <button type="button" class="btn-next" onclick="nextStep(3)">Suivant →</button>
                    </div>
                </div>

                <!-- Étape 3: Options supplémentaires -->
                <div class="form-step" data-step="3">
                    <h2 class="step-title">3. Options supplémentaires</h2>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="child_seat" id="child_seat">
                            <span>Siège enfant (+10€/jour)</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="child_seat_qty">Nombre de sièges enfant</label>
                        <input type="number" id="child_seat_qty" name="child_seat_qty" class="form-control" min="0" max="3" value="0">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="insurance" id="insurance" required>
                            <span>Assurance tous risques (+15€/jour) *</span>
                        </label>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn-prev" onclick="prevStep(2)">← Précédent</button>
                        <button type="button" class="btn-next" onclick="nextStep(4)">Suivant →</button>
                    </div>
                </div>

                <!-- Étape 4: Documents -->
                <div class="form-step" data-step="4">
                    <h2 class="step-title">4. Documents requis</h2>

                    <div class="documents-checklist">
                        <div class="document-item">
                            <div class="document-icon">🪪</div>
                            <div class="document-info">
                                <h4>Permis de conduire</h4>
                                <p>Permis valide correspondant au type de véhicule</p>
                                <input type="file" name="permis" accept="image/*,.pdf" class="file-input">
                            </div>
                        </div>

                        <div class="document-item">
                            <div class="document-icon">💳</div>
                            <div class="document-info">
                                <h4>Empreinte de carte de crédit</h4>
                                <p>Pour la caution (non débitée)</p>
                                <input type="file" name="carte_credit" accept="image/*,.pdf" class="file-input">
                            </div>
                        </div>

                        <div class="document-item">
                            <div class="document-icon">📇</div>
                            <div class="document-info">
                                <h4>Carte d'identité ou Passeport</h4>
                                <p>Document d'identité en cours de validité</p>
                                <input type="file" name="identite" accept="image/*,.pdf" class="file-input">
                            </div>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn-prev" onclick="prevStep(3)">← Précédent</button>
                        <button type="button" class="btn-next" onclick="nextStep(5)">Suivant →</button>
                    </div>
                </div>

                <!-- Étape 5: Récapitulatif -->
                <div class="form-step" data-step="5">
                    <h2 class="step-title">5. Récapitulatif de votre réservation</h2>

                    <div class="reservation-summary">
                        <div class="summary-item">
                            <strong>Véhicule :</strong>
                            <span id="summary-vehicle">-</span>
                        </div>
                        <div class="summary-item">
                            <strong>Type de contrat :</strong>
                            <span id="summary-contract">-</span>
                        </div>
                        <div class="summary-item">
                            <strong>Période :</strong>
                            <span id="summary-period">-</span>
                        </div>
                        <div class="summary-item">
                            <strong>Tarif :</strong>
                            <span id="summary-tarif">-</span>
                        </div>
                        <div class="summary-item">
                            <strong>Options :</strong>
                            <span id="summary-options">-</span>
                        </div>
                        <div class="summary-item total">
                            <strong>Total estimé :</strong>
                            <span id="summary-total">-</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="accept_terms" required>
                            <span>J'accepte les conditions générales de location *</span>
                        </label>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn-prev" onclick="prevStep(4)">← Précédent</button>
                        <button type="submit" class="btn-submit">Confirmer la réservation</button>
                    </div>
                </div>
            </form>
        </section>

        <!-- SECTION MES RÉSERVATIONS -->
        <section id="mes-reservations" class="dashboard-section">
            <div class="section-header">
                <h1>Mes Réservations</h1>
                <p>Suivez l'état de vos locations</p>
            </div>

            <div class="reservations-list">
                <!-- Réservation active -->
                <div class="reservation-card active">
                    <div class="reservation-header">
                        <span class="reservation-status status-active">En cours</span>
                        <span class="reservation-id">#RES-2024-001</span>
                    </div>
                    <div class="reservation-body">
                        <div class="reservation-vehicle">
                            <span class="vehicle-icon">🚗</span>
                            <div>
                                <h3>Peugeot 508</h3>
                                <p>Berline</p>
                            </div>
                        </div>
                        <div class="reservation-details">
                            <div class="detail-item">
                                <strong>Début :</strong> 10/11/2025
                            </div>
                            <div class="detail-item">
                                <strong>Fin :</strong> 17/11/2025
                            </div>
                            <div class="detail-item">
                                <strong>Kilométrage départ :</strong> 45,230 km
                            </div>
                            <div class="detail-item">
                                <strong>Total :</strong> 315€
                            </div>
                        </div>
                        <div class="reservation-actions">
                            <button class="btn-action" onclick="alert('Voir les détails')">Détails</button>
                            <button class="btn-action" onclick="navigateTo('inspection')">Inspection retour</button>
                        </div>
                    </div>
                </div>

                <!-- Réservation à venir -->
                <div class="reservation-card upcoming">
                    <div class="reservation-header">
                        <span class="reservation-status status-upcoming">À venir</span>
                        <span class="reservation-id">#RES-2024-002</span>
                    </div>
                    <div class="reservation-body">
                        <div class="reservation-vehicle">
                            <span class="vehicle-icon">🏍️</span>
                            <div>
                                <h3>Yamaha MT-07</h3>
                                <p>Moto</p>
                            </div>
                        </div>
                        <div class="reservation-details">
                            <div class="detail-item">
                                <strong>Début :</strong> 20/11/2025
                            </div>
                            <div class="detail-item">
                                <strong>Fin :</strong> 22/11/2025
                            </div>
                            <div class="detail-item">
                                <strong>Total :</strong> 100€
                            </div>
                        </div>
                        <div class="reservation-actions">
                            <button class="btn-action" onclick="alert('Modifier')">Modifier</button>
                            <button class="btn-action btn-danger" onclick="alert('Annuler')">Annuler</button>
                        </div>
                    </div>
                </div>

                <!-- Réservation terminée -->
                <div class="reservation-card completed">
                    <div class="reservation-header">
                        <span class="reservation-status status-completed">Terminée</span>
                        <span class="reservation-id">#RES-2024-000</span>
                    </div>
                    <div class="reservation-body">
                        <div class="reservation-vehicle">
                            <span class="vehicle-icon">🚙</span>
                            <div>
                                <h3>Renault Kadjar</h3>
                                <p>SUV</p>
                            </div>
                        </div>
                        <div class="reservation-details">
                            <div class="detail-item">
                                <strong>Période :</strong> 01/11/2025 - 05/11/2025
                            </div>
                            <div class="detail-item">
                                <strong>Kilométrage :</strong> 32,100 km → 32,350 km
                            </div>
                            <div class="detail-item">
                                <strong>Total :</strong> 260€
                            </div>
                        </div>
                        <div class="reservation-actions">
                            <button class="btn-action" onclick="alert('Télécharger facture')">Facture</button>
                            <button class="btn-action" onclick="alert('Laisser un avis')">Avis</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION DOCUMENTS -->
        <section id="documents" class="dashboard-section">
            <div class="section-header">
                <h1>Mes Documents</h1>
                <p>Gérez vos documents administratifs</p>
            </div>

            <div class="documents-grid">
                <div class="document-card verified">
                    <div class="document-card-icon">🪪</div>
                    <h3>Permis de conduire</h3>
                    <p class="document-status">✅ Vérifié</p>
                    <p class="document-date">Expire le : 15/05/2030</p>
                    <button class="btn-action">Mettre à jour</button>
                </div>

                <div class="document-card verified">
                    <div class="document-card-icon">📇</div>
                    <h3>Carte d'identité</h3>
                    <p class="document-status">✅ Vérifié</p>
                    <p class="document-date">Expire le : 20/08/2028</p>
                    <button class="btn-action">Mettre à jour</button>
                </div>

                <div class="document-card pending">
                    <div class="document-card-icon">💳</div>
                    <h3>Carte de crédit</h3>
                    <p class="document-status">⏳ En attente</p>
                    <p class="document-date">À vérifier</p>
                    <button class="btn-action">Ajouter</button>
                </div>
            </div>

            <div class="upload-section">
                <h2>Ajouter un nouveau document</h2>
                <form class="upload-form">
                    <div class="form-group">
                        <label for="doc_type">Type de document</label>
                        <select id="doc_type" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <option value="permis">Permis de conduire</option>
                            <option value="identite">Carte d'identité / Passeport</option>
                            <option value="carte">Carte de crédit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="doc_file">Fichier (PDF ou Image)</label>
                        <input type="file" id="doc_file" class="file-input" accept="image/*,.pdf">
                    </div>
                    <button type="submit" class="btn-primary">Télécharger</button>
                </form>
            </div>
        </section>

        <!-- SECTION INSPECTION -->
        <section id="inspection" class="dashboard-section">
            <div class="section-header">
                <h1>Inspection du Véhicule</h1>
                <p>Documentez l'état du véhicule avant et après la location</p>
            </div>

            <div class="inspection-type-selector">
                <button class="inspection-type-btn active" onclick="selectInspectionType('depart')">
                    Inspection de départ
                </button>
                <button class="inspection-type-btn" onclick="selectInspectionType('retour')">
                    Inspection de retour
                </button>
            </div>

            <form class="inspection-form">
                @csrf

                <div class="form-group">
                    <label for="reservation_select">Sélectionner une réservation *</label>
                    <select id="reservation_select" name="reservation_id" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="1">#RES-2024-001 - Peugeot 508</option>
                        <option value="2">#RES-2024-002 - Yamaha MT-07</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="km_counter">Relevé kilométrique *</label>
                    <input type="number" id="km_counter" name="km_counter" class="form-control" placeholder="Ex: 45230" required>
                </div>

                <div class="form-group">
                    <label for="fuel_level">Niveau de carburant *</label>
                    <select id="fuel_level" name="fuel_level" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="empty">Vide</option>
                        <option value="quarter">1/4</option>
                        <option value="half">1/2</option>
                        <option value="three-quarters">3/4</option>
                        <option value="full">Plein</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>État général du véhicule</label>
                    <div class="condition-checkboxes">
                        <label class="checkbox-label">
                            <input type="checkbox" name="condition[]" value="propre">
                            <span>Propre</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="condition[]" value="rayures">
                            <span>Rayures visibles</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="condition[]" value="bosses">
                            <span>Bosses</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="condition[]" value="impact">
                            <span>Impact pare-brise</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Photos du véhicule (minimum 4 - Avant, Arrière, Côté gauche, Côté droit)</label>
                    <div class="photo-upload-grid">
                        <div class="photo-upload-item">
                            <label for="photo_avant" class="photo-upload-label">
                                <div class="photo-placeholder">
                                    <span class="photo-icon">📸</span>
                                    <span>Avant</span>
                                </div>
                            </label>
                            <input type="file" id="photo_avant" name="photos[]" accept="image/*" class="photo-input" capture="environment">
                        </div>

                        <div class="photo-upload-item">
                            <label for="photo_arriere" class="photo-upload-label">
                                <div class="photo-placeholder">
                                    <span class="photo-icon">📸</span>
                                    <span>Arrière</span>
                                </div>
                            </label>
                            <input type="file" id="photo_arriere" name="photos[]" accept="image/*" class="photo-input" capture="environment">
                        </div>

                        <div class="photo-upload-item">
                            <label for="photo_gauche" class="photo-upload-label">
                                <div class="photo-placeholder">
                                    <span class="photo-icon">📸</span>
                                    <span>Côté gauche</span>
                                </div>
                            </label>
                            <input type="file" id="photo_gauche" name="photos[]" accept="image/*" class="photo-input" capture="environment">
                        </div>

                        <div class="photo-upload-item">
                            <label for="photo_droit" class="photo-upload-label">
                                <div class="photo-placeholder">
                                    <span class="photo-icon">📸</span>
                                    <span>Côté droit</span>
                                </div>
                            </label>
                            <input type="file" id="photo_droit" name="photos[]" accept="image/*" class="photo-input" capture="environment">
                        </div>

                        <div class="photo-upload-item">
                            <label for="photo_tableau" class="photo-upload-label">
                                <div class="photo-placeholder">
                                    <span class="photo-icon">📸</span>
                                    <span>Tableau de bord</span>
                                </div>
                            </label>
                            <input type="file" id="photo_tableau" name="photos[]" accept="image/*" class="photo-input" capture="environment">
                        </div>

                        <div class="photo-upload-item">
                            <label for="photo_autre" class="photo-upload-label">
                                <div class="photo-placeholder">
                                    <span class="photo-icon">📸</span>
                                    <span>Autre</span>
                                </div>
                            </label>
                            <input type="file" id="photo_autre" name="photos[]" accept="image/*" class="photo-input" capture="environment">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes et observations</label>
                    <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="Ajoutez vos commentaires ici..."></textarea>
                </div>

                <div class="penalty-info">
                    <h3>⚠️ Pénalités de retard</h3>
                    <ul>
                        <li>Retard de 1 à 2 heures : +20€</li>
                        <li>Retard de 2 à 6 heures : +50€</li>
                        <li>Retard de plus de 6 heures : Facturation d'une journée supplémentaire</li>
                        <li>Véhicule non rendu : Déclaration de vol après 48h</li>
                    </ul>
                </div>

                <button type="submit" class="btn-primary">Enregistrer l'inspection</button>
            </form>
        </section>

        <!-- SECTION PROFIL -->
        <section id="profil" class="dashboard-section">
            <div class="section-header">
                <h1>Mon Profil</h1>
                <p>Gérez vos informations personnelles</p>
            </div>

            <form class="profile-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="profile_first_name">Prénom</label>
                        <input type="text" id="profile_first_name" name="first_name" class="form-control" value="{{ Auth::user()->first_name }}">
                    </div>
                    <div class="form-group">
                        <label for="profile_last_name">Nom</label>
                        <input type="text" id="profile_last_name" name="last_name" class="form-control" value="{{ Auth::user()->last_name }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile_email">Email</label>
                    <input type="email" id="profile_email" name="email" class="form-control" value="{{ Auth::user()->email }}">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="profile_phone">Téléphone</label>
                        <input type="tel" id="profile_phone" name="phone_number" class="form-control" value="{{ Auth::user()->phone_number }}">
                    </div>
                    <div class="form-group">
                        <label for="profile_dob">Date de naissance</label>
                        <input type="text" id="profile_dob" name="date_of_birth" class="form-control" value="{{ Auth::user()->date_of_birth }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile_address">Adresse</label>
                    <input type="text" id="profile_address" name="address_line1" class="form-control" value="{{ Auth::user()->address_line1 }}">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="profile_postal">Code postal</label>
                        <input type="text" id="profile_postal" name="postal_code" class="form-control" value="{{ Auth::user()->postal_code }}">
                    </div>
                    <div class="form-group">
                        <label for="profile_city">Ville</label>
                        <input type="text" id="profile_city" name="city" class="form-control" value="{{ Auth::user()->city }}">
                    </div>
                </div>

                <hr>

                <h3>Changer le mot de passe</h3>

                <div class="form-group">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="new_password_confirmation" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            </form>
        </section>

    </main>
</div>

<script>
// Navigation entre les sections
function navigateTo(section) {
    // Masquer toutes les sections
    document.querySelectorAll('.dashboard-section').forEach(s => {
        s.classList.remove('active');
    });

    // Retirer la classe active de tous les liens
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });

    // Afficher la section demandée
    document.getElementById(section).classList.add('active');

    // Activer le lien correspondant
    document.querySelector(`[data-section="${section}"]`).classList.add('active');

    // Scroll vers le haut
    document.querySelector('.dashboard-main').scrollTop = 0;
}

// Event listeners pour la navigation
document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        navigateTo(section);
    });
});

// Sélection de véhicule depuis le catalogue
function selectVehicle(model, type) {
    document.getElementById('vehicle_type').value = type;
    document.getElementById('vehicle_model').value = model;
    navigateTo('reservation');
}

// Navigation entre les étapes du formulaire
let currentStep = 1;

function nextStep(step) {
    // Validation basique
    const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
    let valid = true;

    inputs.forEach(input => {
        if (!input.value) {
            input.classList.add('is-invalid');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    if (!valid) {
        alert('Veuillez remplir tous les champs requis');
        return;
    }

    // Cacher l'étape actuelle
    currentStepEl.classList.remove('active');

    // Afficher la nouvelle étape
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    currentStep = step;

    // Scroll vers le haut
    document.querySelector('.dashboard-main').scrollTop = 0;

    // Mettre à jour le récapitulatif si on arrive à l'étape 5
    if (step === 5) {
        updateSummary();
    }
}

function prevStep(step) {
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    currentStep = step;
    document.querySelector('.dashboard-main').scrollTop = 0;
}

function updateSummary() {
    const vehicle = document.getElementById('vehicle_model').value || '-';
    const contractType = document.getElementById('contract_type').value === 'pro' ? 'Contrat professionnel' : 'Location simple';
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const tarifType = document.getElementById('tarif_type').value;
    const childSeat = document.getElementById('child_seat').checked;
    const insurance = document.getElementById('insurance').checked;

    document.getElementById('summary-vehicle').textContent = vehicle;
    document.getElementById('summary-contract').textContent = contractType;
    document.getElementById('summary-period').textContent = `${startDate} → ${endDate}`;
    document.getElementById('summary-tarif').textContent = tarifType || '-';

    let options = [];
    if (childSeat) options.push('Siège enfant');
    if (insurance) options.push('Assurance tous risques');
    document.getElementById('summary-options').textContent = options.length > 0 ? options.join(', ') : 'Aucune';

    // Calcul approximatif (à personnaliser selon vos tarifs)
    document.getElementById('summary-total').textContent = '350€ (estimation)';
}

// Filtrage du catalogue
document.getElementById('type-filter')?.addEventListener('change', function() {
    const selectedType = this.value;
    document.querySelectorAll('.vehicle-card').forEach(card => {
        if (!selectedType || card.getAttribute('data-type') === selectedType) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Type d'inspection
function selectInspectionType(type) {
    document.querySelectorAll('.inspection-type-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Prévisualisation des photos
document.querySelectorAll('.photo-input').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const label = input.previousElementSibling || input.parentElement.querySelector('label');
                const placeholder = label.querySelector('.photo-placeholder');
                placeholder.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
            };
            reader.readAsDataURL(file);
        }
    });
});

// Soumission du formulaire de réservation
document.getElementById('reservationForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Réservation envoyée ! (À connecter avec votre backend Laravel)');
    // Ici, vous ajouterez l'envoi AJAX vers votre contrôleur Laravel
});
</script>
@endsection
