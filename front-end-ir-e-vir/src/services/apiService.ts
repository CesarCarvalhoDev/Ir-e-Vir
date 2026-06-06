import { http } from '@/api/http'
import type { AuthResponse, Charge, EntryPayload, ExitPayload, Fine, Id, LinkVehiclePayload, PaymentResult, Stay, User, Vehicle, Zone } from '@/types/api'

const unwrap = <T>(value: T | { data: T }): T => typeof value === 'object' && value !== null && 'data' in value ? value.data : value

export const apiService = {
  login: async (payload: { email: string; password: string }) => (await http.post<AuthResponse>('/auth/login', payload)).data,
  me: async () => (await http.get<{ user: User }>('/auth/me')).data.user,
  logout: async () => (await http.post<{ message: string }>('/auth/logout')).data,

  getMyVehicles: async () => unwrap((await http.get<Vehicle[] | { data: Vehicle[] }>('/me/vehicles')).data),
  getMyStays: async () => unwrap((await http.get<Stay[] | { data: Stay[] }>('/me/stays')).data),
  getMyCharges: async () => unwrap((await http.get<Charge[] | { data: Charge[] }>('/me/charges')).data),
  getMyFines: async () => unwrap((await http.get<Fine[] | { data: Fine[] }>('/me/fines')).data),
  linkMyVehicle: async (payload: LinkVehiclePayload) => unwrap((await http.post<Vehicle | { data: Vehicle }>('/me/vehicles', payload)).data),
  payMyCharge: async (chargeId: Id) => (await http.post<PaymentResult>(`/me/charges/${chargeId}/pay`)).data,

  getUsers: async () => unwrap((await http.get<User[] | { data: User[] }>('/admin/users')).data),
  getZones: async () => (await http.get<Zone[]>('/admin/zones')).data,
  getVehicles: async () => (await http.get<Vehicle[]>('/admin/vehicles')).data,
  getStays: async () => (await http.get<Stay[]>('/admin/stays')).data,
  createEntry: async (payload: EntryPayload) => (await http.post<Stay>('/admin/stays/entry', payload)).data,
  createExit: async (payload: ExitPayload) => (await http.post('/admin/stays/exit', payload)).data,
  getCharges: async () => unwrap((await http.get<Charge[] | { data: Charge[] }>('/admin/charges')).data),
  getFines: async () => unwrap((await http.get<Fine[] | { data: Fine[] }>('/admin/fines')).data),
  getChargeByPlate: async (plate: string) => unwrap((await http.get<Charge | { data: Charge }>(`/admin/charges/${encodeURIComponent(plate)}`)).data),
  createCharge: async (stayId: Id) => unwrap((await http.post<Charge | { data: Charge }>(`/admin/charges/${stayId}`)).data),
  payCharge: async (chargeId: Id, userId: Id) => (await http.post<PaymentResult>(`/admin/charges/${chargeId}/pay/${userId}`)).data,
  linkVehicle: async (userId: Id, payload: LinkVehiclePayload) => (await http.post<Vehicle>(`/admin/user/${userId}/vehicles`, payload)).data,
  getUserStays: async (userId: Id) => (await http.get<Stay[]>(`/admin/user/${userId}/stays`)).data,
}
