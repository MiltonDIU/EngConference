<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\SubTrack;
use Illuminate\Database\Seeder;

class TrackSubTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            "Track 1 — Colonial Modernity, Biopolitics, and Precarity" => [
                "Colonial classification of bodies/nature; “nature/culture” split as governance",
                "Extraction, dispossession, plantation/logistics, and “resource” thinking",
                "Slow violence, neoliberal modernity, environmental injustice",
                "Precarity/vulnerability/grievability; whose lives count as “grievable”",
                "Biopolitical fractures, borders, policing, surveillance, displacement",
                "Alienation/reification; the human–earth relation as a broken social relation (metabolic rift as lived experience)"
            ],
            "Track 2 — More-than-Human Agency, Posthuman Ecologies, and the Uncanny" => [
                "Nature “as brute” vs nature as agentic, sensing, communicative",
                "Nonhuman agency/consciousness; multispecies life; kinship beyond the human",
                "Hybrid ecologies and networks (human/nonhuman/technology/infrastructure)",
                "Metabolic rift as narrative: broken relations, repair imaginaries, ethical care",
                "Posthumanism and environmental ethics; animacy, voice, refusal",
                "Water/river/ocean agencies (especially useful for delta contexts)"
            ],
            "Track 3 — Eco-cultural Rifts, Water/Landscapes, and Environmental Justice" => [
                "How form carries ethics: voice, narration, witnessing, silence, spectacle vs accountability",
                "Delta imaginaries: tides, storms, mangroves, wetlands, river-life",
                "Climate displacement, migration, borders, “who belongs where”",
                "Corporate/imperial infrastructures as characters (dams, ships, pipelines, data systems)",
                "Archive, memory, and repair: how texts hold harm without consuming it",
                "Myth/fable/folklore as ecological thinking (not “primitive,” but alternative knowledge systems)"
            ],
            "Track 4 — Linguistics, ELT Pedagogy, and Sustainability Literacy" => [
                "Agency in grammar and phenomenological worldviews: subject/object/verb; transitivity and “who acts” in texts",
                "Ecolinguistics: stories we live by; ecological metaphors; discourses of “resources,” “development,” “waste”",
                "Polyphony and dialogism between the human and non-human",
                "Anthropomorphic and naturo-morphic imaginations",
                "Critical discourse analysis of climate/extraction narratives (media, policy, textbooks)",
                "Classroom texts that animate the non-human (fables/myths) and what that trains ethically",
                "Language, animation, digital technology: memes, shorts, dubbing/subtitling, AI voices, eco-narratives online",
                "Curriculum design: SDG-aligned learning outcomes (especially 4.7, 13.3) without moralizing"
            ]
        ];

        foreach ($data as $trackName => $subTracks) {
            $track = Track::create(['name' => $trackName]);
            foreach ($subTracks as $subTrackName) {
                SubTrack::create([
                    'track_id' => $track->id,
                    'name' => $subTrackName
                ]);
            }
        }
    }
}
