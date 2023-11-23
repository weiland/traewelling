<?php

namespace App\Models;

use App\Casts\UTCDateTime;
use App\Enum\TimeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

/**
 * @property int           $id
 * @property int           $status_id
 * @property HafasTrip     $HafasTrip
 * @property TrainStopover $origin_stopover
 * @property TrainStopover $destination_stopover
 * @property TrainStation  $originStation
 * @property TrainStation  $destinationStation
 *
 * @todo rename table to "Checkin" (without Train - we have more than just trains)
 * @todo merge model with "Status" because the difference between trip sources (HAFAS,
 *        User, and future sources) should be handled in the Trip model.
 */
class TrainCheckin extends Model
{

    use HasFactory;

    protected $fillable = [
        'status_id', 'user_id', 'trip_id', 'origin', 'destination',
        'distance', 'duration', 'departure', 'manual_departure', 'arrival', 'manual_arrival', 'points', 'forced',
    ];
    protected $hidden   = ['created_at', 'updated_at'];
    protected $appends  = ['origin_stopover', 'destination_stopover', 'speed'];
    protected $casts    = [
        'id'               => 'integer',
        'status_id'        => 'integer',
        'user_id'          => 'integer',
        'origin'           => 'integer',
        'destination'      => 'integer',
        'distance'         => 'integer',
        'duration'         => 'integer',
        'departure'        => UTCDateTime::class,
        'manual_departure' => UTCDateTime::class,
        'arrival'          => UTCDateTime::class,
        'manual_arrival'   => UTCDateTime::class,
        'points'           => 'integer',
        'forced'           => 'boolean',
    ];

    public function status(): BelongsTo {
        return $this->belongsTo(Status::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function originStation(): HasOne {
        return $this->hasOne(TrainStation::class, 'ibnr', 'origin');
    }

    public function destinationStation(): HasOne {
        return $this->hasOne(TrainStation::class, 'ibnr', 'destination');
    }

    public function HafasTrip(): HasOne {
        return $this->hasOne(HafasTrip::class, 'trip_id', 'trip_id');
    }

    public function getOriginStopoverAttribute(): TrainStopover {
        $stopOver = $this->HafasTrip->stopovers->where('train_station_id', $this->originStation->id)
                                               ->where('departure_planned', $this->departure)
                                               ->first();
        if ($stopOver == null) {
            //To support legacy data, where we don't save the stopovers in the stopovers table, yet.
            Log::info('TrainCheckin #' . $this->id . ': Origin stopover not found. Created a new one.');
            $stopOver = TrainStopover::updateOrCreate(
                [
                    "trip_id"          => $this->trip_id,
                    "train_station_id" => $this->originStation->id
                ],
                [
                    "departure_planned" => $this->departure,
                    "arrival_planned"   => $this->departure,
                ]
            );
            $this->HafasTrip->load('stopovers');
        }
        return $stopOver;
    }

    public function getDestinationStopoverAttribute(): TrainStopover {
        $stopOver = $this->HafasTrip->stopovers->where('train_station_id', $this->destinationStation->id)
                                               ->where('arrival_planned', $this->arrival)
                                               ->first();
        if ($stopOver == null) {
            //To support legacy data, where we don't save the stopovers in the stopovers table, yet.
            Log::info('TrainCheckin #' . $this->id . ': Destination stopover not found. Created a new one.');
            $stopOver = TrainStopover::updateOrCreate(
                [
                    "trip_id"          => $this->trip_id,
                    "train_station_id" => $this->destinationStation->id
                ],
                [
                    "departure_planned" => $this->arrival,
                    "arrival_planned"   => $this->arrival,
                ]
            );
            $this->HafasTrip->load('stopovers');
        }
        return $stopOver;
    }

    /**
     * Takes the planned and optionally real and manual times and returns an array to use while displaying the status.
     * Precedence: Manual > Real > Planned.
     * Only returns the 'original' planned time if the updated time differs from the planned time.
     */
    private static function prepareDisplayTime($planned, $real = NULL, $manual = NULL): array {
        if (isset($manual)) {
            $time = $manual;
            $type = TimeType::MANUAL;
        } elseif (isset($real)) {
            $time = $real;
            $type = TimeType::REALTIME;
        } else {
            $time = $planned;
            $type = TimeType::PLANNED;
        }
        return array(
            'time' => $time,
            'original' => ($planned->toString() !== $time->toString()) ? $planned : NULL,
            'type' => $type
        );
    }

    public function getDisplayDepartureAttribute(): \stdClass {
        $planned = $this->origin_stopover?->departure_planned
            ?? $this->origin_stopover?->departure
            ?? $this->departure;
        $real = $this->origin_stopover?->departure_real;
        $manual = $this->manual_departure;
        return (object) self::prepareDisplayTime($planned, $real, $manual);
    }

    public function getDisplayArrivalAttribute(): \stdClass {
        $planned = $this->destination_stopover?->arrival_planned
            ?? $this->destination_stopover?->arrival
            ?? $this->arrival;
        $real = $this->destination_stopover?->arrival_real;
        $manual = $this->manual_arrival;
        return (object) self::prepareDisplayTime($planned, $real, $manual);
    }

    /**
     * Overwrite the default getter to return the cached value if available
     * @return int
     */
    public function getDurationAttribute(): int {
        //If the duration is already calculated and saved, return it
        if (isset($this->attributes['duration']) && $this->attributes['duration'] !== null) {
            return $this->attributes['duration'];
        }

        //Else calculate and cache it
        $departure = $this->manual_departure ?? $this->origin_stopover->departure ?? $this->departure;
        $arrival   = $this->manual_arrival ?? $this->destination_stopover->arrival ?? $this->arrival;
        $duration  = $arrival->diffInMinutes($departure);
        DB::table('train_checkins')->where('id', $this->id)->update(['duration' => $duration]);
        return $duration;
    }

    public function getSpeedAttribute(): float {
        return $this->duration === 0 ? 0 : ($this->distance / 1000) / ($this->duration / 60);
    }

    /**
     * @return Collection
     * @todo Sichtbarkeit der CheckIns prüfen! Hier werden auch Private CheckIns angezeigt
     */
    public function getAlsoOnThisConnectionAttribute(): Collection {
        return self::with(['status'])
                   ->where([
                               ['status_id', '<>', $this->status->id],
                               ['trip_id', '=', $this->HafasTrip->trip_id],
                               ['arrival', '>', $this->departure],
                               ['departure', '<', $this->arrival]
                           ])
                   ->get()
                   ->map(function(TrainCheckin $trainCheckin) {
                       return $trainCheckin->status;
                   })
                   ->filter(function($status) {
                       return $status !== null && Gate::forUser(Auth::user())->allows('view', $status);
                   });
    }
}
