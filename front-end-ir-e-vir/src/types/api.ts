export type Id = number
export type UserRole = 'user' | 'admin'
export interface Wallet { id: Id; balance: number; status: string }
export interface User { id: Id; name: string; email: string; role: UserRole; available_balance: number; wallet?: Wallet | null; vehicles?: Vehicle[]; created_at: string; updated_at: string }
export interface Zone { id: Id; name: string; maximum_time: number; created_at: string; updated_at: string }
export interface Vehicle { id: Id; plate: string; type: string | null; has_registration: boolean; available_balance?: number; created_at: string; updated_at: string }
export interface Stay { id: Id; entry: string; exit: string | null; total_time: number | null; status: string; vehicle_id: Id; zone_id: Id; created_at: string; updated_at: string }
export interface Payment { id: Id; paid_value: number | string; payment_date: string; status: string; charges_id?: Id; charge_id?: Id; created_at?: string; updated_at?: string }
export interface Charge { id: Id; value: number; status: string; due_date: string; stay_id: Id; user_id?: Id | null; created_at: string; updated_at: string; stay?: Stay; vehicle?: Vehicle; payment?: Payment; payments?: Payment[] }
export interface Fine { id: Id; user_id: Id; stay_id: Id; amount: number; reason: string; status: string; started_at: string; resolved_at: string | null; paid_at: string | null; created_at: string; updated_at: string; stay?: Stay; vehicle?: Vehicle; user?: Pick<User, 'id' | 'name' | 'email' | 'role'> }
export interface EntryPayload { entry: string; plate: string; zone_id: Id }
export interface ExitPayload { exit: string; plate: string }
export interface LinkVehiclePayload { plate: string; type: string }
export interface PaymentResult { message: string; charge: Charge; payment: Payment; remaining_balance: string }
export interface AuthResponse { message: string; token: string; token_type: 'Bearer'; user: User }
