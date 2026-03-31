<?php

namespace Database\Seeders;

use App\Models\Committee;
use App\Models\CommitteeType;
use App\Models\ConferenceMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommitteeManagementSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Committee Types
        $typeAcademicRows = CommitteeType::updateOrCreate(['name' => 'Academic Advisory Boards']);
        $typeConferenceRows = CommitteeType::updateOrCreate(['name' => 'Conference Committee Structure']);

        // 2. Academic Advisory Boards Root
        $rootAcademic = Committee::updateOrCreate([
            'committee_type_id' => $typeAcademicRows->id,
            'name' => 'Academic Advisory Boards',
            'parent_id' => 0,
            'sort_order' => 1,
        ]);

        $this->seedAdvisoryBoards($typeAcademicRows, $rootAcademic);

        // 3. Conference Committee Root Sections
        $this->seedConferenceCommittee($typeConferenceRows);
    }

    private function seedAdvisoryBoards($type, $parent)
    {
        $subCommittees = [
            'Advisory Board A: National' => [
                ['name' => 'Tahmina Ahmed', 'affiliation' => 'Professor, Department of English, University of Dhaka'],
                ['name' => 'Dr. Maswood Akhter', 'affiliation' => 'Professor, Department of English, University of Rajshahi'],
                ['name' => 'Dr. Fakrul Alam', 'affiliation' => 'Advisor, Department of English, East West University'],
                ['name' => 'Dr. Firdous Azim', 'affiliation' => 'Professor, Department of English and Humanities, BRAC University'],
                ['name' => 'Dr. Binoy Barman', 'affiliation' => 'Professor, Department of English, Daffodil International University'],
                ['name' => 'Dr. Khaliquzzaman Elias', 'affiliation' => 'Professor, Department of English and Modern Languages, North South University'],
                ['name' => 'GH Habib', 'affiliation' => 'Assistant Professor, Department of English, University of Chittagong'],
                ['name' => 'Mashrur Shahid Hossain', 'affiliation' => 'Professor, Department of English, Jahangirnagar University'],
                ['name' => 'Dr. Sabiha Huq', 'affiliation' => 'Professor, Department of English and Humanities, BRAC University'],
                ['name' => 'Dr. Razia Sultana Khan', 'affiliation' => 'Advisor and Professor, Department of English and Modern Languages, Independent University Bangladesh'],
                ['name' => 'Dr. Md. Shamim Mondol', 'affiliation' => 'Chairperson and Associate Professor, Department of English, Green University'],
                ['name' => 'Dr. Shamsad Mortuza', 'affiliation' => 'Vice-Chancellor and Professor, University of Liberal Arts Bangladesh (ULAB)'],
                ['name' => 'Dr. Khan Touseef Osman', 'role' => 'Chair', 'affiliation' => 'Associate Professor, Department of English, Daffodil International University'],
                ['name' => 'Dr. Mahfuza Parveen', 'affiliation' => 'Associate Professor, Environmental Science and Disaster Management & Director, Division of Research, Daffodil International University'],
                ['name' => 'AMM Hamidur Rahman', 'affiliation' => 'Professor, Department of English, Daffodil International University'],
                ['name' => 'Dr. M Shahidullah', 'affiliation' => 'Dean and Professor, Faculty of Arts and Social Sciences, Green University'],
                ['name' => 'Dr. Raihan Sharif', 'affiliation' => 'Chairperson and Professor, Department of English, Jahangirnagar University'],
            ],
            'Advisory Board B: International' => [
                ['name' => 'Dr. Zuraina Binti Ali', 'affiliation' => 'Associate Professor & Dean, Center for Modern Languages, Universiti Malaysia Pahang, Al-Sultan Abdullah, Malaysia'],
                ['name' => 'Dr. Yadu Gyawali', 'affiliation' => 'Director, International Relations, Mid-West University, Nepal'],
                ['name' => 'Dr. Kaish Q Khan', 'affiliation' => 'Associate Professor, Kareer School, KIIT University, Bhubaneswar, India'],
                ['name' => 'Dr. Himadri Lahiri', 'affiliation' => 'Professor, Department of English, School of Humanities, Netaji Subhas Open University (NSOU), India'],
                ['name' => 'Dr. Khan Touseef Osman', 'role' => 'Chair', 'affiliation' => 'Associate Professor, Department of English, Daffodil International University'],
                ['name' => 'Dr. Ahlam Othman', 'affiliation' => 'Professor & Head, Department of English, Al-Azhar University, Egypt'],
                ['name' => 'Dr. Khrishnan Unni P', 'affiliation' => 'Professor, Department of English, Deshbandhu College, University of Delhi'],
                ['name' => 'Dr. Avishek Parui', 'affiliation' => 'Associate Professor, Department of Humanities and Social Sciences, Indian Institute of Technology (IIT), Madras, India', 'remarks' => 'Consent pending'],
                ['name' => 'Dr. Jnanu Paudel', 'affiliation' => 'Assistant Professor of English Education, Far Western University, Nepal'],
                ['name' => 'Dr. Patricia Coloma Penate', 'affiliation' => 'Academic Secretary, Department of English, Universidad Nacional de Educación a Distancia Education (UNED), Spain'],
                ['name' => 'Dr. Patricia Coloma Penate', 'affiliation' => 'Associate Professor, Department of Theatre and Film Studies, Rivers State University, Nigeria'],
                ['name' => 'Dr. Muhammad Sharif Uddin', 'affiliation' => 'Director, MA in Teaching Program, & Assistant Professor, Teacher Education and Professional Development, Morgan State University, USA'],
            ]
        ];

        $cOrder = 1;
        foreach ($subCommittees as $name => $members) {
            $committee = Committee::updateOrCreate([
                'committee_type_id' => $type->id,
                'name' => $name,
                'parent_id' => $parent->id,
            ], [
                'sort_order' => $cOrder++,
            ]);

            $mOrder = 1;
            foreach ($members as $m) {
                $member = $this->getOrCreateMember($m['name'], $m['affiliation']);
                $committee->members()->syncWithoutDetaching([
                    $member->id => [
                        'role' => $m['role'] ?? null,
                        'remarks' => $m['remarks'] ?? null,
                        'sort_order' => $mOrder++,
                    ]
                ]);
            }
        }
    }

    private function seedConferenceCommittee($type)
    {
        // Root Committees with sort_order
        $execLead = Committee::updateOrCreate([
            'committee_type_id' => $type->id,
            'name' => 'Executive Leadership',
            'parent_id' => 0,
        ], ['sort_order' => 1]);

        $academicProg = Committee::updateOrCreate([
            'committee_type_id' => $type->id,
            'name' => 'Academic and Programme Committees',
            'parent_id' => 0,
        ], ['sort_order' => 2]);

        $opsMgmt = Committee::updateOrCreate([
            'committee_type_id' => $type->id,
            'name' => 'Operations and Management',
            'parent_id' => 0,
        ], ['sort_order' => 3]);

        $this->seedExecutiveLeadership($type, $execLead);
        $this->seedAcademicProgramme($type, $academicProg);
        $this->seedOperationsManagement($type, $opsMgmt);
    }

    private function seedExecutiveLeadership($type, $parent)
    {
        $subs = [
            'Honorary' => [
                ['role' => 'Chief Patron', 'name' => 'Dr. Md. Sabur Khan', 'affiliation' => 'Chairman, BoT, Daffodil International University'],
                ['role' => 'Conference Patron', 'name' => 'Professor Dr. M. R. Kabir', 'affiliation' => 'Vice Chancellor, Daffodil International University'],
                ['role' => 'Chief Advisor', 'name' => 'Samiha Khan', 'affiliation' => 'Director, Daffodil Family'],
            ],
            'Governance' => [
                ['role' => 'Administrative Advisory Board Chair', 'name' => 'Professor Dr. Liza Sharmin', 'affiliation' => 'Dean, Faculty of Humanities and Social Sciences'],
                ['role' => 'Administrative Advisory Board, Co-Chair', 'name' => 'Professor Dr. Kudrat-E-Khuda Babu', 'affiliation' => 'Associate Dean, Faculty of Humanities and Social Sciences'],
                ['role' => 'Academic Advisory Board Chair', 'name' => 'Associate Professor Dr. Khan Touseef Osman', 'affiliation' => 'Associate Professor, Daffodil International University'],
            ]
        ];

        $cOrder = 1;
        foreach ($subs as $subName => $members) {
            $committee = Committee::updateOrCreate(['committee_type_id' => $type->id, 'name' => $subName, 'parent_id' => $parent->id], ['sort_order' => $cOrder++]);
            $mOrder = 1;
            foreach ($members as $m) {
                $member = $this->getOrCreateMember($m['name'], $m['affiliation']);
                $committee->members()->syncWithoutDetaching([$member->id => ['role' => $m['role'], 'sort_order' => $mOrder++]]);
            }
        }
    }

    private function seedAcademicProgramme($type, $parent)
    {
        $subs = [
            'Publications & Proceedings' => [
                ['role' => 'Chair', 'name' => 'Nuruzzaman Moral', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Lead', 'name' => 'Nasif Khalid Swadheen', 'affiliation' => 'Lecturer, Daffodil International University'],
            ],
            'Executive' => [
                ['role' => 'Conference Chair', 'name' => 'Dr. Ehatasham Ul Hoque Eiten', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Conference Co-Chair', 'name' => 'Dr. Mohammad Rahmatullah', 'affiliation' => 'Assistant Professor, Daffodil International University'],
            ],
            'Academic' => [
                ['role' => 'Academic Programme Chair', 'name' => 'Md. Ariful Islam Laskar', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Academic Programme Co-Chair', 'name' => 'Shamsi Ara Huda', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Academic Programme Co-Chair', 'name' => 'Rabeya Binte Habib', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Academic Programme Co-Chair', 'name' => 'Dr. Israk Zahan Papia', 'affiliation' => 'Lecturer, Daffodil International University'],
                ['role' => 'Lead', 'name' => 'Kallol Bain', 'affiliation' => 'Lecturer, Daffodil International University'],
            ],
            'Operational' => [
                ['role' => 'Organising Committee Chair', 'name' => 'Associate Professor, Tahsina Yasmin', 'affiliation' => 'Associate Professor, Daffodil International University'],
                ['role' => 'Organising Committee Co-Chair', 'name' => 'Fatema Begum Laboni', 'affiliation' => 'Assistant Professor, Daffodil International University'],
            ],
            'Administration' => [
                ['role' => 'Conference Administrator', 'name' => 'Mohammad Elius Hossain', 'affiliation' => 'Assistant Professor, Daffodil International University'],
            ]
        ];

        $cOrder = 1;
        foreach ($subs as $subName => $members) {
            $committee = Committee::updateOrCreate(['committee_type_id' => $type->id, 'name' => $subName, 'parent_id' => $parent->id], ['sort_order' => $cOrder++]);
            $mOrder = 1;
            foreach ($members as $m) {
                $member = $this->getOrCreateMember($m['name'], $m['affiliation']);
                $committee->members()->syncWithoutDetaching([$member->id => ['role' => $m['role'], 'sort_order' => $mOrder++]]);
            }
        }
    }

    private function seedOperationsManagement($type, $parent)
    {
        $committees = [
            'Finance & Budget' => [
                ['role' => 'Chair', 'name' => 'Irina Ishrat', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Co-Chair', 'name' => 'Emran Khan', 'affiliation' => 'Sr. Lecturer, Daffodil International University'],
            ],
            'Registration & Delegate Services' => [
                ['role' => 'Chair', 'name' => 'Md. Rafiz Uddin', 'affiliation' => 'Sr. Lecturer, Daffodil International University'],
            ],
            'IT & Digital Support' => [
                ['role' => 'Lead', 'name' => 'Mahmudul Hasan', 'affiliation' => 'Lecturer, Daffodil International University'],
            ],
            'Communications & Public Engagement' => [
                ['role' => 'Lead', 'name' => 'Mahinur Akther', 'affiliation' => 'Lecturer, Daffodil International University'],
                ['role' => 'Co-Lead', 'name' => 'Afroza Akter', 'affiliation' => 'Lecturer, Daffodil International University'],
            ],
            'Sponsorship & Development' => [
                ['role' => 'Lead', 'name' => 'Mustafizur Rahman Sameen', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Co-Lead', 'name' => 'Farjana Yesmin', 'affiliation' => 'Lecturer, Daffodil International University'],
            ],
            'Volunteer Coordination' => [
                ['role' => 'Lead', 'name' => 'Rubaiaat -E- Tarik', 'affiliation' => 'Lecturer, Daffodil International University'],
                ['role' => 'Co-Lead', 'name' => 'Delwar Jahan', 'affiliation' => 'Lecturer, Daffodil International University'],
            ],
            'Local Arrangements & Hospitality' => [
                ['role' => 'Chair', 'name' => 'Asma Alam', 'affiliation' => 'Assistant Professor, Daffodil International University'],
                ['role' => 'Lead', 'name' => 'Zerin Tohfa Farhat', 'affiliation' => 'Lecturer, Daffodil International University'],
            ]
        ];

        $cOrder = 1;
        foreach ($committees as $subName => $members) {
            $committee = Committee::updateOrCreate(['committee_type_id' => $type->id, 'name' => $subName, 'parent_id' => $parent->id], ['sort_order' => $cOrder++]);
            $mOrder = 1;
            foreach ($members as $m) {
                $member = $this->getOrCreateMember($m['name'], $m['affiliation']);
                $committee->members()->syncWithoutDetaching([$member->id => ['role' => $m['role'], 'sort_order' => $mOrder++]]);
            }
        }
    }

    private function getOrCreateMember($name, $affiliation)
    {
        $name = strip_tags(str_replace(['**', '*'], '', $name));
        $affiliation = trim($affiliation);
        
        $designation = null;
        $institution = $affiliation;

        if (Str::contains($affiliation, ',')) {
            $parts = explode(',', $affiliation);
            $designation = trim(array_shift($parts));
            $institution = trim(implode(',', $parts));
        }

        return ConferenceMember::updateOrCreate(
            ['name' => $name],
            [
                'designation' => $designation,
                'institution' => $institution,
                'is_active' => true,
            ]
        );
    }
}
