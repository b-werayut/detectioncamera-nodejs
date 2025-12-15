/*
  Warnings:

  - You are about to drop the `TmstCameraDetectionLogs` table. If the table is not empty, all the data it contains will be lost.
  - You are about to drop the `TmstPowerDetectionLogs` table. If the table is not empty, all the data it contains will be lost.

*/
BEGIN TRY

BEGIN TRAN;

-- DropTable
DROP TABLE [dbo].[TmstCameraDetectionLogs];

-- DropTable
DROP TABLE [dbo].[TmstPowerDetectionLogs];

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
