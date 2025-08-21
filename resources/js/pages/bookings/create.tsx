import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import InputError from '@/components/input-error';

interface Room {
    id: number;
    number: string;
    type: string;
    description: string;
    capacity: number;
    price_per_night: number;
    amenities: string[] | null;
}

interface Props {
    rooms: Room[];
    checkInDate?: string;
    checkOutDate?: string;
    guests?: string;
    errors: Record<string, string>;
    [key: string]: unknown;
}

export default function CreateBooking({ rooms, checkInDate, checkOutDate, guests, errors }: Props) {
    const { data, setData, post, processing } = useForm({
        room_id: '',
        guest_name: '',
        guest_email: '',
        guest_phone: '',
        check_in_date: checkInDate || '',
        check_out_date: checkOutDate || '',
        number_of_guests: guests || '1',
        special_requests: '',
    });

    const selectedRoom = rooms.find(room => room.id.toString() === data.room_id);

    const calculateTotal = () => {
        if (!selectedRoom || !data.check_in_date || !data.check_out_date) return 0;
        
        const checkIn = new Date(data.check_in_date);
        const checkOut = new Date(data.check_out_date);
        const nights = Math.max(1, Math.ceil((checkOut.getTime() - checkIn.getTime()) / (1000 * 60 * 60 * 24)));
        
        return nights * selectedRoom.price_per_night;
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bookings.store'));
    };

    return (
        <AppShell>
            <Head title="Create Booking" />
            
            <div className="container mx-auto px-4 py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold mb-2">📅 Create New Booking</h1>
                    <p className="text-gray-600">Complete your room reservation</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Booking Form */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Booking Details</CardTitle>
                                <CardDescription>Please fill in your booking information</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={handleSubmit} className="space-y-6">
                                    {/* Room Selection */}
                                    <div>
                                        <Label htmlFor="room_id">Select Room</Label>
                                        <Select value={data.room_id} onValueChange={(value) => setData('room_id', value)}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Choose a room" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {rooms.map((room) => (
                                                    <SelectItem key={room.id} value={room.id.toString()}>
                                                        Room {room.number} - {room.type} (${room.price_per_night}/night)
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors.room_id} />
                                    </div>

                                    {/* Guest Information */}
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <Label htmlFor="guest_name">Guest Name</Label>
                                            <Input
                                                id="guest_name"
                                                type="text"
                                                value={data.guest_name}
                                                onChange={(e) => setData('guest_name', e.target.value)}
                                                required
                                            />
                                            <InputError message={errors.guest_name} />
                                        </div>
                                        <div>
                                            <Label htmlFor="guest_email">Email Address</Label>
                                            <Input
                                                id="guest_email"
                                                type="email"
                                                value={data.guest_email}
                                                onChange={(e) => setData('guest_email', e.target.value)}
                                                required
                                            />
                                            <InputError message={errors.guest_email} />
                                        </div>
                                    </div>

                                    <div>
                                        <Label htmlFor="guest_phone">Phone Number</Label>
                                        <Input
                                            id="guest_phone"
                                            type="tel"
                                            value={data.guest_phone}
                                            onChange={(e) => setData('guest_phone', e.target.value)}
                                            required
                                        />
                                        <InputError message={errors.guest_phone} />
                                    </div>

                                    {/* Booking Dates */}
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <Label htmlFor="check_in_date">Check-in Date</Label>
                                            <Input
                                                id="check_in_date"
                                                type="date"
                                                value={data.check_in_date}
                                                onChange={(e) => setData('check_in_date', e.target.value)}
                                                min={new Date().toISOString().split('T')[0]}
                                                required
                                            />
                                            <InputError message={errors.check_in_date} />
                                        </div>
                                        <div>
                                            <Label htmlFor="check_out_date">Check-out Date</Label>
                                            <Input
                                                id="check_out_date"
                                                type="date"
                                                value={data.check_out_date}
                                                onChange={(e) => setData('check_out_date', e.target.value)}
                                                min={data.check_in_date || new Date().toISOString().split('T')[0]}
                                                required
                                            />
                                            <InputError message={errors.check_out_date} />
                                        </div>
                                    </div>

                                    <div>
                                        <Label htmlFor="number_of_guests">Number of Guests</Label>
                                        <Select 
                                            value={data.number_of_guests} 
                                            onValueChange={(value) => setData('number_of_guests', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {Array.from({ length: selectedRoom?.capacity || 8 }, (_, i) => i + 1).map(num => (
                                                    <SelectItem key={num} value={num.toString()}>
                                                        {num} {num === 1 ? 'Guest' : 'Guests'}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors.number_of_guests} />
                                    </div>

                                    <div>
                                        <Label htmlFor="special_requests">Special Requests (Optional)</Label>
                                        <Textarea
                                            id="special_requests"
                                            value={data.special_requests}
                                            onChange={(e) => setData('special_requests', e.target.value)}
                                            placeholder="Any special requests or preferences..."
                                            rows={3}
                                        />
                                        <InputError message={errors.special_requests} />
                                    </div>

                                    <div className="flex gap-4">
                                        <Button type="submit" disabled={processing} className="flex-1">
                                            {processing ? 'Creating Booking...' : '📅 Confirm Booking'}
                                        </Button>
                                        <Button type="button" variant="outline" onClick={() => window.history.back()}>
                                            Cancel
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Booking Summary */}
                    <div>
                        <Card className="sticky top-4">
                            <CardHeader>
                                <CardTitle>📋 Booking Summary</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {selectedRoom ? (
                                    <div className="space-y-4">
                                        <div>
                                            <h3 className="font-semibold text-lg">Room {selectedRoom.number}</h3>
                                            <p className="text-gray-600 capitalize">{selectedRoom.type}</p>
                                            <p className="text-sm text-gray-500 mt-2">{selectedRoom.description}</p>
                                        </div>

                                        {selectedRoom.amenities && (
                                            <div>
                                                <h4 className="font-semibold mb-2">Amenities:</h4>
                                                <div className="flex flex-wrap gap-1">
                                                    {selectedRoom.amenities.slice(0, 6).map((amenity, index) => (
                                                        <Badge key={index} variant="outline" className="text-xs">
                                                            {amenity}
                                                        </Badge>
                                                    ))}
                                                </div>
                                            </div>
                                        )}

                                        {data.check_in_date && data.check_out_date && (
                                            <div className="border-t pt-4">
                                                <div className="space-y-2">
                                                    <div className="flex justify-between">
                                                        <span>Check-in:</span>
                                                        <span>{new Date(data.check_in_date).toLocaleDateString()}</span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span>Check-out:</span>
                                                        <span>{new Date(data.check_out_date).toLocaleDateString()}</span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span>Nights:</span>
                                                        <span>
                                                            {Math.max(1, Math.ceil((new Date(data.check_out_date).getTime() - new Date(data.check_in_date).getTime()) / (1000 * 60 * 60 * 24)))}
                                                        </span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span>Guests:</span>
                                                        <span>{data.number_of_guests}</span>
                                                    </div>
                                                </div>

                                                <div className="border-t pt-2 mt-4">
                                                    <div className="flex justify-between items-center">
                                                        <span className="font-semibold">Total:</span>
                                                        <span className="font-bold text-xl text-green-600">
                                                            ${calculateTotal().toFixed(2)}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <p className="text-gray-500">Select a room to see booking summary</p>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppShell>
    );
}