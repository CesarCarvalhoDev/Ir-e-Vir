export const money = (value: number | string | null | undefined) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(value ?? 0))
export const dateTime = (value: string | null | undefined) => value ? new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(value)) : 'Não informado'
export const localDateTimeValue = () => { const now = new Date(); now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); return now.toISOString().slice(0, 16) }
const statusLabels: Record<string, string> = { ACTIVE: 'Ativa', FINISHED: 'Finalizada', IRREGULAR: 'Irregular', PENDING: 'Pendente', PAID: 'Paga', OVERDUE: 'Vencida' }
export const statusLabel = (status: string) => statusLabels[status] ?? status
