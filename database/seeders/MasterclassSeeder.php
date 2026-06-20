<?php

namespace Database\Seeders;

use App\Models\Masterclass;
use App\Models\TrainingRegistration;
use App\Models\TrainingSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterclassSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------------
        // Masterclass 1 — Niveau Débutant
        // ---------------------------------------------------------------
        $mc1 = Masterclass::create([
            'title'       => 'Les Fondamentaux du Wedding Planning',
            'slug'        => 'fondamentaux-wedding-planning',
            'niveau'      => 'Débutant',
            'description' => 'Une journée complète pour acquérir les bases du métier de wedding planner : de la rencontre client à la coordination le jour J.',
            'programme'   => "Matin : découverte du métier, profil client, budget\nAprès-midi : planning, fournisseurs, coordination jour J",
            'is_active'   => true,
        ]);

        $session1 = TrainingSession::create([
            'masterclass_id' => $mc1->id,
            'start_date'     => now()->subDays(40)->setTime(9, 0),
            'end_date'       => now()->subDays(40)->setTime(17, 0),
            'location_type'  => 'presentiel',
            'adresse'        => 'Institut Sophie Weddings Dreams, Ngor Almadies, Dakar',
            'price'          => 75000,
            'max_participants' => 15,
            'is_active'      => true,
        ]);

        // ---------------------------------------------------------------
        // Masterclass 2 — Niveau Avancé
        // ---------------------------------------------------------------
        $mc2 = Masterclass::create([
            'title'       => 'Gestion de Crise & Perfectionnement Wedding',
            'slug'        => 'gestion-crise-perfectionnement-wedding',
            'niveau'      => 'Avancé',
            'description' => 'Pour les wedding planners confirmés : gérer l\'imprévu, optimiser ses marges, fidéliser sa clientèle haut de gamme.',
            'programme'   => "Matin : gestion de crise, plan B, communication\nAprès-midi : positionnement luxe, tarification, portfolio",
            'is_active'   => true,
        ]);

        $session2 = TrainingSession::create([
            'masterclass_id' => $mc2->id,
            'start_date'     => now()->subDays(15)->setTime(10, 0),
            'end_date'       => now()->subDays(15)->setTime(18, 0),
            'location_type'  => 'online',
            'online_link'    => 'https://zoom.us/j/demo-sophie-weddings',
            'replay_url'     => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'price'          => 95000,
            'max_participants' => 12,
            'is_active'      => true,
        ]);

        // ---------------------------------------------------------------
        // Participants — 10 par session, 7 confirmés
        // ---------------------------------------------------------------
        $participants = [
            // 7 confirmés
            ['name' => 'Aminata Diallo',     'email' => 'aminata.diallo@gmail.com',    'phone' => '+221 77 123 45 67', 'confirmed' => true],
            ['name' => 'Fatou Mbaye',        'email' => 'fatou.mbaye@outlook.com',     'phone' => '+221 76 234 56 78', 'confirmed' => true],
            ['name' => 'Mariama Sow',        'email' => 'mariama.sow@gmail.com',       'phone' => '+221 78 345 67 89', 'confirmed' => true],
            ['name' => 'Coumba Ndiaye',      'email' => 'coumba.ndiaye@yahoo.fr',      'phone' => '+221 77 456 78 90', 'confirmed' => true],
            ['name' => 'Rokhaya Fall',       'email' => 'rokhaya.fall@gmail.com',      'phone' => '+221 76 567 89 01', 'confirmed' => true],
            ['name' => 'Astou Diagne',       'email' => 'astou.diagne@hotmail.com',    'phone' => '+221 78 678 90 12', 'confirmed' => true],
            ['name' => 'Ndèye Sarr',         'email' => 'ndeye.sarr@gmail.com',        'phone' => '+221 77 789 01 23', 'confirmed' => true],
            // 3 non confirmés
            ['name' => 'Khady Diouf',        'email' => 'khady.diouf@gmail.com',       'phone' => '+221 76 890 12 34', 'confirmed' => false],
            ['name' => 'Binta Camara',       'email' => 'binta.camara@outlook.com',    'phone' => '+221 78 901 23 45', 'confirmed' => false],
            ['name' => 'Seynabou Gueye',     'email' => 'seynabou.gueye@gmail.com',    'phone' => '+221 77 012 34 56', 'confirmed' => false],
        ];

        $participants2 = [
            // 7 confirmés
            ['name' => 'Isabelle Mensah',    'email' => 'isabelle.mensah@gmail.com',   'phone' => '+221 76 111 22 33', 'confirmed' => true],
            ['name' => 'Sophie Tine',        'email' => 'sophie.tine@outlook.com',     'phone' => '+221 77 222 33 44', 'confirmed' => true],
            ['name' => 'Adja Balde',         'email' => 'adja.balde@gmail.com',        'phone' => '+221 78 333 44 55', 'confirmed' => true],
            ['name' => 'Mame Diarra Faye',   'email' => 'mamediarra.faye@yahoo.fr',    'phone' => '+221 76 444 55 66', 'confirmed' => true],
            ['name' => 'Aissatou Ba',        'email' => 'aissatou.ba@gmail.com',       'phone' => '+221 77 555 66 77', 'confirmed' => true],
            ['name' => 'Penda Cissé',        'email' => 'penda.cisse@hotmail.com',     'phone' => '+221 78 666 77 88', 'confirmed' => true],
            ['name' => 'Dieynaba Diop',      'email' => 'dieynaba.diop@gmail.com',     'phone' => '+221 76 777 88 99', 'confirmed' => true],
            // 3 non confirmés
            ['name' => 'Rama Thiaw',         'email' => 'rama.thiaw@gmail.com',        'phone' => '+221 77 888 99 00', 'confirmed' => false],
            ['name' => 'Yacine Toure',       'email' => 'yacine.toure@outlook.com',    'phone' => '+221 78 999 00 11', 'confirmed' => false],
            ['name' => 'Marème Lo',          'email' => 'mareme.lo@gmail.com',         'phone' => '+221 76 000 11 22', 'confirmed' => false],
        ];

        foreach ($participants as $p) {
            TrainingRegistration::create([
                'training_session_id' => $session1->id,
                'name'          => $p['name'],
                'email'         => $p['email'],
                'phone'         => $p['phone'],
                'is_confirmed'  => $p['confirmed'],
                'confirmed_at'  => $p['confirmed'] ? now()->subDays(35) : null,
                'created_at'    => now()->subDays(50),
                'updated_at'    => now()->subDays(35),
            ]);
        }

        foreach ($participants2 as $p) {
            TrainingRegistration::create([
                'training_session_id' => $session2->id,
                'name'          => $p['name'],
                'email'         => $p['email'],
                'phone'         => $p['phone'],
                'is_confirmed'  => $p['confirmed'],
                'confirmed_at'  => $p['confirmed'] ? now()->subDays(10) : null,
                'created_at'    => now()->subDays(25),
                'updated_at'    => now()->subDays(10),
            ]);
        }

        // ---------------------------------------------------------------
        // Résumé affiché en console
        // ---------------------------------------------------------------
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║           MASTERCLASS SEEDER — COMPTES DE TEST              ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║  Connexion via : /mon-espace  (lien magique par email)       ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║  MASTERCLASS 1 — Fondamentaux Wedding (Débutant)            ║');
        $this->command->info('║  Session : ' . now()->subDays(40)->format('d/m/Y') . ' · Présentiel · Dakar              ║');
        $this->command->info('╠──────────────────────────────────────────────────────────────╣');
        $this->command->info('║  ✅ CONFIRMÉS (attestation disponible) :                     ║');

        $confirmed1 = array_filter($participants, fn($p) => $p['confirmed']);
        foreach ($confirmed1 as $p) {
            $line = '║     ' . str_pad($p['name'], 22) . '  ' . $p['email'];
            $this->command->info(str_pad($line, 63) . ' ║');
        }

        $this->command->info('║  ⏳ EN ATTENTE :                                             ║');
        $pending1 = array_filter($participants, fn($p) => !$p['confirmed']);
        foreach ($pending1 as $p) {
            $line = '║     ' . str_pad($p['name'], 22) . '  ' . $p['email'];
            $this->command->info(str_pad($line, 63) . ' ║');
        }

        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║  MASTERCLASS 2 — Gestion de Crise (Avancé)                  ║');
        $this->command->info('║  Session : ' . now()->subDays(15)->format('d/m/Y') . ' · En ligne · Replay disponible      ║');
        $this->command->info('╠──────────────────────────────────────────────────────────────╣');
        $this->command->info('║  ✅ CONFIRMÉS (attestation disponible) :                     ║');

        $confirmed2 = array_filter($participants2, fn($p) => $p['confirmed']);
        foreach ($confirmed2 as $p) {
            $line = '║     ' . str_pad($p['name'], 22) . '  ' . $p['email'];
            $this->command->info(str_pad($line, 63) . ' ║');
        }

        $this->command->info('║  ⏳ EN ATTENTE :                                             ║');
        $pending2 = array_filter($participants2, fn($p) => !$p['confirmed']);
        foreach ($pending2 as $p) {
            $line = '║     ' . str_pad($p['name'], 22) . '  ' . $p['email'];
            $this->command->info(str_pad($line, 63) . ' ║');
        }

        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->newLine();
    }
}
