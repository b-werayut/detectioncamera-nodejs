/*
  Warnings:

  - You are about to alter the column `picstatus` on the `TmstCameraDetectionLogs` table. The data in that column could be lost. The data in that column will be cast from `NVarChar(1000)` to `Int`.
  - You are about to alter the column `vdostatus` on the `TmstCameraDetectionLogs` table. The data in that column could be lost. The data in that column will be cast from `NVarChar(1000)` to `Int`.

*/
BEGIN TRY

BEGIN TRAN;

-- AlterTable
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [picstatus] INT NOT NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [vdostatus] INT NOT NULL;

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
