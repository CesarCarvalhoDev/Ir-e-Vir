export type Id = number
export type UserRole = 'user' | 'admin'
export interface Wallet { id: Id; balance: number; status: string }
export interface User { id: Id; name: string; email: string; role: UserRole; available_balance: number; wallet?: Wallet | null; vehicles?: Vehicle[]; created_at: string; updated_at: string }
export interface Zone { id: Id; name: string; maximum_time: number; created_at: string; updated_at: string }
export interface Vehicle { id: Id; plate: string; type: string | null; has_registration: boolean; available_balance?: number; created_at: string; updated_at: string }
export interface Stay { id: Id; entry: string; exit: string | null; total_time: number | null; status: string; vehicle_id: Id; zone_id: Id; created_at: string; updated_at: string }
export interface Charge { id: Id; value: number; status: string; due_date: string; stay_id: Id; created_at: string; updated_at: string; stay?: Stay; vehicle?: Vehicle }
export interface EntryPayload { entry: string; plate: string; zone_id: Id }
export interface ExitPayload { exit: string; plate: string }
export interface LinkVehiclePayload { plate: string; type: string }
export interface PaymentResult { message: string; charge: Charge; payment: { id: Id; paid_value: string; payment_date: string; status: string }; remaining_balance: string }
export interface AuthResponse { message: string; token: string; token_type: 'Bearer'; user: User }
