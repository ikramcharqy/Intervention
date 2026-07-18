<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formulaire;
use App\Models\TypeIntervention;
use App\Models\Intervention;
use App\Models\Chantier;
use App\Models\Emplacement;
use App\Models\User;
use Carbon\Carbon;

class FormulaireSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère quelques types d'interventions existants
        $types = TypeIntervention::where('is_active', true)->take(3)->get();
        if ($types->count() < 3) {
            return; // Pas assez de types
        }

        // --- Formulaire 1 : Installation Fibre ---
        $form1 = Formulaire::create([
            'type_intervention_id' => $types[0]->id,
            'nom' => 'Rapport d\'Installation Fibre',
            'description' => 'À remplir obligatoirement lors d\'une installation de fibre optique.',
            'is_active' => true,
        ]);

        $form1->questions()->createMany([
            ['question' => 'Numéro de série du boîtier', 'type_reponse' => 'Texte', 'obligatoire' => true, 'ordre' => 1],
            ['question' => 'Longueur de câble utilisée (mètres)', 'type_reponse' => 'Nombre', 'obligatoire' => true, 'ordre' => 2],
            ['question' => 'Test de signal optique OK ?', 'type_reponse' => 'OuiNon', 'obligatoire' => true, 'ordre' => 3],
            ['question' => 'Photo de l\'installation', 'type_reponse' => 'Photo', 'obligatoire' => true, 'ordre' => 4],
            ['question' => 'Commentaires supplémentaires', 'type_reponse' => 'TexteLong', 'obligatoire' => false, 'ordre' => 5],
            ['question' => 'Signature du client', 'type_reponse' => 'Signature', 'obligatoire' => true, 'ordre' => 6],
        ]);

        // --- Formulaire 2 : Maintenance ---
        $form2 = Formulaire::create([
            'type_intervention_id' => $types[1]->id,
            'nom' => 'Fiche de Maintenance Préventive',
            'description' => 'Checklist de maintenance.',
            'is_active' => true,
        ]);

        $form2->questions()->createMany([
            ['question' => 'Date du dernier contrôle', 'type_reponse' => 'Date', 'obligatoire' => false, 'ordre' => 1],
            ['question' => 'Niveau d\'encrassement', 'type_reponse' => 'Liste', 'obligatoire' => true, 'ordre' => 2],
            ['question' => 'Action effectuée', 'type_reponse' => 'Checkbox', 'obligatoire' => true, 'ordre' => 3],
        ]);
        
        $form2->questions()->where('question', 'Niveau d\'encrassement')->first()->choix()->createMany([
            ['valeur' => 'Faible', 'libelle' => 'Faible', 'ordre' => 1],
            ['valeur' => 'Moyen', 'libelle' => 'Moyen', 'ordre' => 2],
            ['valeur' => 'Élevé', 'libelle' => 'Élevé', 'ordre' => 3],
        ]);

        $form2->questions()->where('question', 'Action effectuée')->first()->choix()->createMany([
            ['valeur' => 'Nettoyage', 'ordre' => 1],
            ['valeur' => 'Mise à jour firmware', 'ordre' => 2],
            ['valeur' => 'Remplacement pièce', 'ordre' => 3],
        ]);

        // --- Formulaire 3 : Audit de sécurité ---
        $form3 = Formulaire::create([
            'type_intervention_id' => $types[2]->id,
            'nom' => 'Audit Sécurité Caméras',
            'description' => 'Vérification des angles et de la vision nocturne.',
            'is_active' => true,
        ]);

        $form3->questions()->createMany([
            ['question' => 'Modèle de la caméra', 'type_reponse' => 'Texte', 'obligatoire' => true, 'ordre' => 1],
            ['question' => 'Qualité d\'image', 'type_reponse' => 'Radio', 'obligatoire' => true, 'ordre' => 2],
            ['question' => 'Coordonnées GPS exactes', 'type_reponse' => 'GPS', 'obligatoire' => false, 'ordre' => 3],
        ]);

        $form3->questions()->where('question', 'Qualité d\'image')->first()->choix()->createMany([
            ['valeur' => 'Excellente', 'ordre' => 1],
            ['valeur' => 'Bonne', 'ordre' => 2],
            ['valeur' => 'Médiocre', 'ordre' => 3],
            ['valeur' => 'Aucune image', 'ordre' => 4],
        ]);

        // --- Création de 10 Interventions ---
        $chantier = Chantier::first();
        $emplacement = Emplacement::where('chantier_id', $chantier->id)->first() ?? Emplacement::first();
        $technicien = User::role('technicien')->first();
        $admin = User::role('admin')->first() ?? User::first();

        if (!$chantier || !$technicien) return;

        for ($i = 1; $i <= 10; $i++) {
            $type = $types->random();
            
            Intervention::create([
                'code_intervention' => 'INT-' . date('Ym') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'chantier_id' => $chantier->id,
                'emplacement_id' => $emplacement->id,
                'technicien_id' => $technicien->id,
                'type_intervention_id' => $type->id,
                'cree_par' => $admin->id,
                'priorite' => ['Faible', 'Normale', 'Haute', 'Urgente'][rand(0, 3)],
                'statut' => 'Planifiee',
                'date_prevue_debut' => Carbon::now()->addDays(rand(1, 10))->setHour(rand(8, 16))->setMinute(0),
                'date_prevue_fin' => Carbon::now()->addDays(rand(1, 10))->setHour(rand(17, 19))->setMinute(0),
                'description' => 'Intervention générée automatiquement pour les tests.',
            ]);
        }
    }
}
