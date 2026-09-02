---
description: "OQARI 7-Layer Feature Completion Standard"
globs: "**/*.php"
---
# OQARI 7-Layer Feature Standard

Setiap fitur TIDAK BOLEH dianggap selesai jika hanya menyentuh UI -> Controller -> Model. Setiap fitur baru wajib memenuhi 7 layer berikut secara berurutan:
1. **Database:** Migration terpadu, tidak ada schema drift, explicit keys & constraints.
2. **Model:** Relasi terdefinisi (`hasMany/belongsTo`), fillables akurat, Traits terpasang.
3. **Validation:** FormRequest / array rules ketat (hindari trust-client).
4. **Authorization:** Policy & RBAC (pastikan user berhak mengakses row ini).
5. **Service/Action:** Logic domain tidak di Controller, gunakan `Action` pipeline (`CreatePosOrderAction`, dll).
6. **Controller/UI:** Routing bersih, UI terintegrasi.
7. **Test:** PHPUnit / Pest Test lengkap.
